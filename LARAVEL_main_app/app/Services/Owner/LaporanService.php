<?php

// app/Services/ProductService.php
namespace App\Services\Owner;

use App\Http\Resources\DistribusiResource;
use App\Http\Resources\LotBahanBakuResource;
use App\Http\Resources\LotBahanBakuServiceResource;
use App\Http\Resources\ProduksiResource;
use App\Http\Resources\RestokBahanBakuResource;
use App\Models\Distribusi;
use App\Models\LotBahanBaku;
use App\Models\Produksi;
use App\Models\RestokBahanBaku;
use App\Services\StafAdmin\RecallProdukService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

use function Termwind\parse;

class LaporanService
{
    protected $recallProdukService;
    
    public function __construct(RecallProdukService $recallProdukService)
    {
        $this->recallProdukService = $recallProdukService;
    }

    public function indexProduksi(String $search, int $paginate, array $filter = [])
    {
        try {
            $produksi = Produksi::with([
               'master_produk', 
            ]);
            $produksi = $produksi->where('is_archived', false);
            $produksi = $produksi->where(function (Builder $query) use($search) {
               $query
               ->where('nomor_batch', 'like', '%'.$search.'%')
               ->orWhere('status', '=', $search)
               ->orWhere('kuantitas', '=', $search)
               ->orWhere(function (Builder $qdate) use ($search) {
                    $qdate->whereYear('mulai_produksi', $search)
                        ->orWhereMonth('mulai_produksi', $search)
                        ->orWhereDay('mulai_produksi', $search)
                        ->orWhereDate('mulai_produksi', $search)
                        ->orWhereTime('mulai_produksi', '>', $search);
                })
                ->orWhere(function (Builder $qdate) use ($search) {
                    $qdate->whereYear('selesai_produksi', $search)
                        ->orWhereMonth('selesai_produksi', $search)
                        ->orWhereDay('selesai_produksi', $search)
                        ->orWhereDate('selesai_produksi', $search)
                        ->orWhereTime('selesai_produksi', '>', $search);
                })
                ->orWhere(function (Builder $qdate) use ($search) {
                    $qdate->whereYear('tanggal_kadaluarsa', $search)
                        ->orWhereMonth('tanggal_kadaluarsa', $search)
                        ->orWhereDay('tanggal_kadaluarsa', $search)
                        ->orWhereDate('tanggal_kadaluarsa', $search);
                })
                ->orWhereHas('master_produk', function (Builder $qmp) use($search) {
                   $qmp
                   ->where('nama_produk', 'like', '%'.$search.'%')
                   ->orWhere('kode_produk', 'like', '%'.$search.'%');
                });
            });

            // FILTERS
            if(isset($filter['sort'])){
                $sort = $filter['sort'];
                $field = Produksi::getField($sort['field']);
                $order = $sort['order'];
                if($field){
                    $produksi = $produksi->orderBy($field, $order);
                }
            }else{
                $produksi = $produksi->orderBy('created_at', 'desc');
            }
            
            if(isset($filter['range'])){
                $range = $filter['range'];
                $start = Carbon::parse($range['time'][0])->startOfDay();
                $end = Carbon::parse($range['time'][1])->endOfDay();
                $field = Produksi::getField($range['field']);
                if($field){
                    $produksi = $produksi->whereBetween($field, [$start, $end]);
                }
            }

            $produksi = $produksi->paginate($paginate);
            $result = ProduksiResource::collection($produksi);
            return $result;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function indexRestok(String $search, int $paginate, array $filter = [])
    {
        try {
            $restok = RestokBahanBaku::with([
                'master_bahan_baku',
                'master_bahan_baku.supplier',
            ]);
            $restok = $restok->where('is_archived', false);
            $restok = $restok->where(function (Builder $query) use($search) {
               $query
               ->where('kode_restok', 'like', '%'.$search.'%')
               ->orWhere('jumlah_restok', '=', $search)
               ->orWhere(function (Builder $qdate) use ($search) {
                    $qdate->whereYear('tanggal_restok', $search)
                        ->orWhereMonth('tanggal_restok', $search)
                        ->orWhereDay('tanggal_restok', $search)
                        ->orWhereDate('tanggal_restok', $search);
                })
               ->orWhereHas('master_bahan_baku', function (Builder $qb) use($search) {
                   $qb
                   ->where('nama_bahan', 'like', '%'.$search.'%')
                   ->orWhere('kode_bahan', 'like', '%'.$search.'%')
                   ->orWhereHas('supplier', function (Builder $qs) use($search) {
                       $qs
                       ->where('nama_supplier', 'like', '%'.$search.'%')
                       ->orWhere('kode_supplier', 'like', '%'.$search.'%');
                   });
               });
            });

            // FILTERS
            if(isset($filter['sort'])){
                $sort = $filter['sort'];
                $field = RestokBahanBaku::getField($sort['field']);
                $order = $sort['order'];
                if($field){
                    $restok = $restok->orderBy($field, $order);
                }
            }else{
                $restok = $restok->orderBy('created_at', 'desc');
            }
            
            if(isset($filter['range'])){
                $range = $filter['range'];
                $start = Carbon::parse($range['time'][0])->startOfDay();
                $end = Carbon::parse($range['time'][1])->endOfDay();
                $field = RestokBahanBaku::getField($range['field']);
                if($field){
                    $restok = $restok->whereBetween($field, [$start, $end]);
                }
            }

            $restok = $restok->paginate($paginate);
            $result = RestokBahanBakuResource::collection($restok);
            return $result;
        } catch (\Throwable $th) {
            dd($th);
            return false;
        }
    }

    public function indexLot(String $search, int $paginate, array $filter = [])
    {
        try {
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
        } catch (\Throwable $th) {
            dd($th);
            return false;
        }
    }

    public function indexDistribusi(String $search, int $paginate, array $filter)
    {
        try {
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

            $distribusi = $distribusi->paginate($paginate);
            $distribusi = DistribusiResource::collection($distribusi);
            return $distribusi;
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public function indexRecallProduk(String $search, int $paginate, array $filter)
    {
        try {
            return $this->recallProdukService->getRecallProduk($search, $paginate, $filter);
        } catch (\Throwable $th) {
            dd($th);
            return false;
        }
    }
}