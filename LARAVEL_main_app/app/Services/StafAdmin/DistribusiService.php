<?php

// app/Services/ProductService.php
namespace App\Services\StafAdmin;

use App\Http\Resources\DistribusiResource;
use App\Models\Distribusi;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class DistribusiService
{
    public function index(String $search, int $paginate, array $filter = [])
    {
        return $this->my_getDistribusi($search, $paginate, $filter);
    }
    
    public function my_getDistribusi(String $search = '', int $paginate = 10, array $filter = [])
    {
        $distribusi = Distribusi::where('is_archived', false);
        $distribusi = $distribusi->where(function (Builder $query) use($search) {
            $query
            ->where('nama_retailer', 'like', '%'.$search.'%')
            ->orWhere('kode_distribusi', 'like', '%'.$search.'%')
            ->orWhere('alamat', 'like', '%'.$search.'%')
            ->orWhere(function (Builder $qdate) use ($search) {
                $qdate->whereYear('tanggal_pengiriman', $search)
                    ->orWhereMonth('tanggal_pengiriman', $search)
                    ->orWhereDay('tanggal_pengiriman', $search)
                    ->orWhereDate('tanggal_pengiriman', $search);
            })
            ->orWhereHas('produksi', function (Builder $qp) use($search) {
                $qp
                ->where(function (Builder $qpp) use($search) {
                    $qpp
                    ->where('nomor_batch', 'like', '%'.$search.'%')
                    ->orWhereHas('master_produk', function (Builder $qppmp) use($search) {
                        $qppmp
                        ->where('nama_produk', 'like', '%'.$search.'%');
                    });
                });
            });
        });

        // FILTERS
        if(isset($filter['sort'])){
            $sort = $filter['sort'];
            $field = Distribusi::getField($sort['field']);
            $order = $sort['order'];
            if($field){
                $distribusi = $distribusi->orderBy($field, $order);
            }
        }else{
            $distribusi = $distribusi->orderBy('created_at', 'desc');
        }
        
        if(isset($filter['range'])){
            $range = $filter['range'];
            $start = Carbon::parse($range['time'][0])->startOfDay();
            $end = Carbon::parse($range['time'][1])->endOfDay();
            $field = Distribusi::getField($range['field']);
            if($field){
                $distribusi = $distribusi->whereBetween($field, [$start, $end]);
            }
        }

        $distribusi = $distribusi->with(['produksi', 'retur_produk', 'recall_produk'])->paginate($paginate);
        $distribusi = DistribusiResource::collection($distribusi);
        return $distribusi;
    }

    public function getDistribusi(String $search = '')
    {
        $query = Distribusi::query();

        $query->where('is_archived', false)->where('jumlah_tersisa', '>', 0);

        if ($search) {
            $query->where(function (Builder $query) use ($search) {
                $query
                ->where('nama_retailer', 'like', '%' . $search . '%')
                ->orWhere('kode_distribusi', 'like', '%' . $search . '%')
                ->orWhere('alamat', 'like', '%' . $search . '%')
                ->orWhereHas('produksi', function (Builder $qp) use ($search) {
                    $qp->where(function (Builder $qpp) use ($search) {
                        $qpp->where('nomor_batch', 'like', '%' . $search . '%')
                            ->orWhereHas('master_produk', function (Builder $qppmp) use ($search) {
                                $qppmp->where('nama_produk', 'like', '%' . $search . '%');
                            });
                    });
                })
                ->orWhere(function (Builder $qdate) use ($search) {
                    $qdate->whereYear('tanggal_pengiriman', $search)
                        ->orWhereMonth('tanggal_pengiriman', $search)
                        ->orWhereDay('tanggal_pengiriman', $search)
                        ->orWhereDate('tanggal_pengiriman', $search);
                });
            });
        }

        // Eager load relationships
        $query->with(['produksi', 'retur_produk', 'recall_produk']);

        // Execute the query
        $distribusi = $query->get();

        // Transform to resource
        return DistribusiResource::collection($distribusi)->resolve();
    }

    static public function create(array $data){
        $distribusi = new Distribusi();
        try {
            $distribusi->produksi_id = $data['produksi_id'];
            $distribusi->nama_retailer = $data['nama_retailer'];
            $distribusi->alamat = $data['alamat'];
            $distribusi->jumlah_tersisa = $data['jumlah_tersisa'];
            
            $distribusi->tanggal_pengiriman = Carbon::parse($data['tanggal_pengiriman'])->format('Y-m-d H:i:s');
            $distribusi->save();
            return true;
        } catch (\Throwable $th) {
            dd($th);
            return false;
        }
    }

    static public function update(array $data, Distribusi $distribusi){
        try {
            $distribusi->nama_retailer = $data['nama_retailer'];
            $distribusi->alamat = $data['alamat'];
            $distribusi->tanggal_pengiriman = Carbon::parse($data['tanggal_pengiriman'])->format('Y-m-d H:i:s');
            $distribusi->save();
            return true;
        } catch (\Throwable $th) {
            dd($th);
            return false;
        }
    }

    static public function delete(Distribusi $distribusi){
        try {
            $distribusi->is_archived = true;
            $distribusi->save();

            $distribusi->recall_produk->each(function ($recall_produk) {
                $recall_produk->is_archived = true;
                $recall_produk->save();
            });
            return true;
        } catch (\Throwable $th) {
            dd($th);
            return false;
        }
    }
}