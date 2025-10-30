<?php

// app/Services/ProductService.php
namespace App\Services;

use App\Models\LotPenggunaanLogs;
use App\Models\Produksi;
use Illuminate\Support\Facades\Auth;

class LotPenggunaanLogsService
{
    public function addPenggunaan(array $data, int $produksi_id){
        try {
            foreach($data as $bahan_baku){
                $lot_bahan_baku = new LotPenggunaanLogs();
                $lot_bahan_baku->produksi_id = $produksi_id;
                $lot_bahan_baku->lot_bahan_baku_id = $bahan_baku['lot_bahan_baku_id'];
                $lot_bahan_baku->jumlah = $bahan_baku['jumlah_pakai'];
                $lot_bahan_baku->tanggal_penggunaan = parseDate(now());
                $lot_bahan_baku->user_id = Auth::id();
                $lot_bahan_baku->save();
            }

            return true;
        } catch (\Throwable $th) {
            dd($th);
            return false;
        }
    }

    public function editPenggunaan(array $data, Produksi $produksi)
    {
        // dd($data);
        try {
            $lot_ids = collect($data)->pluck('lot_bahan_baku_id')->toArray();
            // dd($lot_ids);
            foreach($data as $bahan_baku){
                $produksi->lot_penggunaan_logs()->updateOrCreate(
                    ['lot_bahan_baku_id' => $bahan_baku['lot_bahan_baku_id']], 
                    [
                        'jumlah' => $bahan_baku['jumlah_pakai'],
                        'tanggal_penggunaan' => parseDate(now()),
                        'user_id' => Auth::id(),
                    ]
                );
            }

            $produksi->lot_penggunaan_logs()->whereNotIn('lot_bahan_baku_id', $lot_ids)->delete();

            // $produksi->
            // foreach($data as $bahan_baku){
            //     $lot_bahan_baku->produksi_id = $produksi->id;
            //     $lot_bahan_baku->lot_bahan_baku_id = $bahan_baku['lot_bahan_baku_id'];
            //     $lot_bahan_baku->jumlah = $bahan_baku['jumlah_pakai'];
            //     $lot_bahan_baku->tanggal_penggunaan = parseDate(now());
            // }
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function deletePenggunaan(LotPenggunaanLogs $model){
        try {
            $model->delete();
            return true;
        } catch (\Throwable $th) {
            dd($th);
            return false;
        }
    }
}