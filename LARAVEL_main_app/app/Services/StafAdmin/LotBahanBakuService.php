<?php

// app/Services/ProductService.php
namespace App\Services\StafAdmin;

use App\Http\Resources\LotBahanBakuResource;
use App\Http\Resources\LotBahanBakuServiceResource;
use App\Models\LotBahanBaku;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class LotBahanBakuService
{
    static public function index(String $search, int $paginate, array $filter = [])
    {
        try{
            $lot = LotBahanBaku::with([
                'restok_bahan_baku',
                'restok_bahan_baku.master_bahan_baku',
                'restok_bahan_baku.master_bahan_baku.supplier'
            ]);
            $lot = $lot->where('is_archived', false);
            $lot = $lot->where(function (Builder $query) use ($search) {
                $query
                ->where('kode_lot', 'like', '%'.$search.'%')
                ->orWhere('jumlah', 'like', '%'.$search.'%')
                ->orWhere('status', '=', $search)
                ->orWhere(function (Builder $qdate) use ($search) {
                    $qdate->whereYear('tanggal_kadaluarsa', $search)
                        ->orWhereMonth('tanggal_kadaluarsa', $search)
                        ->orWhereDay('tanggal_kadaluarsa', $search)
                        ->orWhereDate('tanggal_kadaluarsa', $search);
                })
                ->orWhereHas('restok_bahan_baku', function (Builder $qrbb) use ($search) {
                    $qrbb
                    ->where('is_archived', false)
                    ->Where('kode_restok', 'like', '%'.$search.'%')
                    ->orWhere('jumlah_restok', '=', $search)
                    ->orWhereHas('master_bahan_baku', function (Builder $qmbb) use ($search) {
                        $qmbb
                        ->where('nama_bahan', 'like', '%'.$search.'%')
                        ->orWhere('kode_bahan', 'like', '%'.$search.'%')
                        ->orWhereHas('supplier', function (Builder $qs) use ($search) {
                            $qs
                            ->where('nama_supplier', 'like', '%'.$search.'%')
                            ->orWhere('kode_supplier', 'like', '%'.$search.'%');
                        });
                    });
                });
            });

            // FILTERS
            if(isset($filter['sort'])){
                $sort = $filter['sort'];
                $field = LotBahanBaku::getField($sort['field']);
                $order = $sort['order'];
                if($field){
                    $lot = $lot->orderBy($field, $order);
                }
            }else{
                $lot = $lot->orderBy('created_at', 'desc');
            }
            
            if(isset($filter['range'])){
                $range = $filter['range'];
                $start = Carbon::parse($range['time'][0])->startOfDay();
                $end = Carbon::parse($range['time'][1])->endOfDay();
                $field = LotBahanBaku::getField($range['field']);
                if($field){
                    $lot = $lot->whereBetween($field, [$start, $end]);
                }
            }

            $lot = $lot->paginate($paginate);
            $result = LotBahanBakuServiceResource::collection($lot);
            return $result;
            return $result;
        }catch (\Exception $th){
            dd($th);
            return false;
        }
    }
}