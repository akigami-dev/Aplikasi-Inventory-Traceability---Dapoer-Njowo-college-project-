<?php

// app/Services/ProductService.php
namespace App\Services\StafAdmin;

use App\Http\Resources\ReturProdukResource;
use App\Models\ReturProduk;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

;

class ReturProdukService
{
    public function index(String $search)
    {
        try{
            $returProduk = ReturProduk::where('is_archived', false);
            $returProduk = $returProduk->where(function (Builder $query) use ($search) {

                $query
                ->where('tindakan', 'like', '%'.$search.'%')
                ->orWhere('jumlah_retur', '=', $search)
                ->orWhereHas('distribusi', function (Builder $qd) use ($search) {
                    $qd
                    ->where('kode_distribusi', 'like', '%'.$search.'%')
                    ->orWhereHas('produksi', function (Builder $qp) use ($search) {
                        $qp
                        ->where('nomor_batch', 'like', '%'.$search.'%');
                    });
                })
                ->orWhereHas('user', function (Builder $qu) use ($search) {
                    $qu->where('name', 'like', '%'.$search.'%');
                });
            });
            $returProduk = $returProduk->get();
            $returProduk = ReturProdukResource::collection($returProduk)->resolve();

            return $returProduk;
        }catch(\Exception $th){
            dd($th);
        }
    }

    public function create(array $request)
    {
        try {
            $returProduk = new ReturProduk();
            $returProduk->distribusi_id = $request['distribusi_id'];
            $returProduk->tanggal_retur = parseDate($request['tanggal_retur'], 'Y-m-d H:i:s');
            $returProduk->jumlah_retur = $request['jumlah_retur'];
            $returProduk->tindakan = $request['tindakan'];
            $returProduk->keterangan = $request['keterangan'];
            $returProduk->catatan_tambahan = $request['catatan_tambahan'];
            $returProduk->user_id = Auth::id();
            $returProduk->save();
            return $returProduk->toArray();
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public function update(ReturProduk $returProduk, array $request)
    {
        try {
            $returProduk->distribusi_id = $request['distribusi_id'];
            $returProduk->tanggal_retur = parseDate($request['tanggal_retur'], 'Y-m-d H:i:s');
            $returProduk->jumlah_retur = $request['jumlah_retur'];
            $returProduk->tindakan = $request['tindakan'];
            $returProduk->keterangan = $request['keterangan'];
            $returProduk->catatan_tambahan = $request['catatan_tambahan'];
            $returProduk->user_id = Auth::id();
            $returProduk->save();
            return $returProduk->toArray();
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public function destroy(ReturProduk $returProduk)
    {
        try {
            $returProduk->is_archived = true;
            $returProduk->save();
            return $returProduk->toArray();
        } catch (\Throwable $th) {
            dd($th);
        }
    }
}