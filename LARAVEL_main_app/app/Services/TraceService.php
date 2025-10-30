<?php

// app/Services/ProductService.php
namespace App\Services;

use App\Http\Resources\ProduksiResource;
use App\Http\Resources\TraceAuthResource;
use App\Http\Resources\TraceResource;
use App\Models\Produksi;
use App\Models\RestokBahanBaku;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class TraceService
{
    public function index(String $trace){
        $produksi = Produksi::where('nomor_batch', $trace)->whereHas('distribusi')->first();
        if(!$produksi) return false;
        // dd($produksi);

        $result = $this->guest($produksi);
        
        return $result;
    }

    protected function guest(Produksi $produksi)
    {
        try{
            $result = new TraceResource($produksi);
            return $result->resolve();
        }catch(Exception $th){
            return false;
        }
    }

    public function authGet()
    {
        $data = RestokBahanBaku::where('is_archived', false);
        if($search) {
            $data->search($search);
        }
        // FILTERS
        if(isset($filter['sort'])){
            $sort = $filter['sort'];
            $field = self::getField($sort['field']);
            $order = $sort['order'];
            if($field){
                $data = $data->orderBy($field, $order);
            }
        }else{
            $data = $data->orderBy('created_at', 'desc');
        }

        if(isset($filter['range'])){
            $range = $filter['range'];
            $start = Carbon::parse($range['time'][0])->startOfDay();
            $end = Carbon::parse($range['time'][1])->endOfDay();
            $field = self::getField($range['field']);
            if($field){
                $data = $data->whereBetween($field, [$start, $end]);
            }
        }
    }

    public function getField(String $field)
    {
        switch ($field) {
            case 'created at':
                return 'created_at';
                break;
            case 'nomor batch':
                return 'nomor_batch';
                break;
            case 'kode distribusi':
                return 'kode_distribusi';
                break;
            case 'nama produk':
                return 'nama_produk';
                break;
            case 'kadaluarsa':
                return 'tanggal_kadaluarsa';
                break;
            case 'tanggal kadaluarsa':
                return 'tanggal_kadaluarsa';
                break;
            default:
                return false;
                break;
        }
    }

    public function authIndex(String $search, Array $filter, int $paginate = 10){
        try{
            $produksi = Produksi::where('produksis.is_archived', false)->has('distribusi');
            $produksi = $produksi->search($search);

            try {
                if(isset($filter['sort'])){
                    $sort = $filter['sort'];
                    $field = self::getField($sort['field']);
                    $order = $sort['order'];
                    if($field){
                        switch ($field) {
                            case 'kode_distribusi':
                                $produksi = $produksi->
                                join('distribusis', function ($join) {
                                    $join->on('distribusis.produksi_id', '=', 'produksis.id')
                                        ->where('distribusis.is_archived', false); // atau 0, tergantung tipe kolom
                                })
                                ->orderBy('distribusis.kode_distribusi', $order)
                                ->select('produksis.*');
                                break;
                            case 'nama_produk':
                                $produksi = $produksi->join('master_produks', 'master_produks.id', '=', 'produksis.master_produk_id')->orderBy('master_produks.nama_produk', $order)->select('produksis.*');
                                break;
                            default:
                                $produksi = $produksi->orderBy($field, $order); 
                                break;
                        }
                    }
                }else{
                    $produksi = $produksi->orderBy('created_at', 'desc');
                }
            } catch (\Throwable $th) {
            }

            try {
                if(isset($filter['range'])){
                    $range = $filter['range'];
                    $start = Carbon::parse($range['time'][0])->startOfDay();
                    $end = Carbon::parse($range['time'][1])->endOfDay();
                    $field = self::getField($range['field']);
                    if($field){
                        $produksi = $produksi->whereBetween($field, [$start, $end]);
                    }
                }
            } catch (\Throwable $th) {
            }

            $produksi = $produksi->paginate($paginate);
            $result = TraceAuthResource::collection($produksi);

            return $result;
        }catch(Exception $th){
            dd($th);
        }
    }
}