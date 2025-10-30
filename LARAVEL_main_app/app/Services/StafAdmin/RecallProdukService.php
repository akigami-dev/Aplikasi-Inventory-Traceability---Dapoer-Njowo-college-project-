<?php

// app/Services/ProductService.php
namespace App\Services\StafAdmin;

use App\Http\Resources\DistribusiResource;
use App\Http\Resources\RecallProdukResource;
use App\Models\Distribusi;
use App\Models\RecallProduk;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class RecallProdukService
{
    public function getAlasanRecall()
    {
        try {
            $alasan_recall = ['mendekati kadaluarsa', 'telah kadaluarsa', 'produk rusak'];
            $alasanDatabase = RecallProduk::where('is_archived', false)->distinct()->pluck('alasan_recall')->toArray();
            foreach ($alasanDatabase as $alasan) {
                if (!in_array($alasan, $alasan_recall)) {
                    $alasan_recall[] = $alasan;
                }
            }
            return $alasan_recall;
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public function getRecallProduk(String $search = '', int $limit = 0, array $filter = [])
    {
        $query = RecallProduk::query();
        $query->where('is_archived', false);

        if ($search){
            $query->where(function (Builder $query) use ($search){
                $query
                ->where('kode_recall', 'like', '%'.$search.'%')
                ->orWhere('alasan_recall', 'like', '%'.$search.'%')
                ->orWhere('jumlah_recall', 'like', '%'.$search.'%')
                ->orWhere('keterangan', 'like', '%'.$search.'%')
                ->orWhere(function (Builder $qdate) use ($search){
                    $qdate
                    ->whereYear('tanggal_recall', $search)
                    ->orWhereMonth('tanggal_recall', $search)
                    ->orWhereDay('tanggal_recall', $search)
                    ->orWhereDate('tanggal_recall', $search)
                    ->orWhereTime('tanggal_recall', '>', $search);
                })
                ->orWhereHas('distribusi', function (Builder $qdist) use ($search){
                    $qdist
                    ->where('kode_distribusi', 'like', '%'.$search.'%');
                })
                ->orWhereHas('user', function(Builder $quser) use ($search){
                    $quser
                    ->where('name', 'like', '%'.$search.'%');
                })
                ;
            });
        }

        // FILTERS
        if(isset($filter['sort'])){
            $sort = $filter['sort'];
            $field = RecallProduk::getField($sort['field']);
            $order = $sort['order'];
            if($field){
                $query = $query->orderBy($field, $order);
            }
        }else{
            $query = $query->orderBy('created_at', 'desc');
        }
        
        if(isset($filter['range'])){
            $range = $filter['range'];
            $start = Carbon::parse($range['time'][0])->startOfDay();
            $end = Carbon::parse($range['time'][1])->endOfDay();
            $field = RecallProduk::getField($range['field']);
            if($field){
                $query = $query->whereBetween($field, [$start, $end]);
            }
        }

        $recallProduk = $query->with(['distribusi', 'user']);

        $recallProduk = $recallProduk->paginate($limit);
        $recallProduk = RecallProdukResource::collection($recallProduk);
        
        return $recallProduk;
    }

    public function saveRecord(array $data, RecallProduk $recallProduk)
    {
        try {
            $recallProduk->distribusi_id = $data['distribusi_id'];
            $recallProduk->tanggal_recall = parseDate($data['tanggal_recall']);
            $recallProduk->jumlah_recall = $data['jumlah_recall'];
            $recallProduk->alasan_recall = $data['alasan_recall'];
            $recallProduk->keterangan = $data['keterangan'];
            $recallProduk->save();
            return $recallProduk;
        } catch (\Throwable $th) {
            dd($th);
            return false;
        }
    }


    public function restoreJumlahTersisa($jumlah_recall, $distribusi_id)
    {
        try {
            $distribusi = Distribusi::find($distribusi_id);
            $distribusi->jumlah_tersisa = $distribusi->jumlah_tersisa + $jumlah_recall;
            // dd($distribusi);
            $distribusi->save();
            return true;
        } catch (\Throwable $th) {
            dd($th);
            return false;
        }
    }


    public function index(String $search, int $paginate, array $filter = [])
    {
        try {
            $recallProduk = $this->getRecallProduk($search, $paginate, $filter);
            
            return $recallProduk;
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    // =================== CRUD ====================

    public function create(Array $data)
    {
        try {
            $recallProduk = new RecallProduk();
            $result = $this->saveRecord($data, $recallProduk);

            return $result;
        } catch (\Throwable $th) {
            dd($th);
            return false;
        }
    }

    public function update(array $data, RecallProduk $recallProduk)
    {
        try {
            $this->restoreJumlahTersisa($recallProduk->jumlah_recall, $recallProduk->distribusi_id);
            $result = $this->saveRecord($data, $recallProduk);

            return $result;
        } catch (\Throwable $th) {
            dd($th);
            return false;
        }
    }

    public function delete(RecallProduk $recallProduk)
    {
        try {
            // dd($recallProduk);
            $this->restoreJumlahTersisa($recallProduk->jumlah_recall, $recallProduk->distribusi_id);
            $recallProduk->is_archived = true;
            $recallProduk->save();
            
            return true;
        } catch (\Throwable $th) {
            dd($th);
            return false;
        }
    }
}