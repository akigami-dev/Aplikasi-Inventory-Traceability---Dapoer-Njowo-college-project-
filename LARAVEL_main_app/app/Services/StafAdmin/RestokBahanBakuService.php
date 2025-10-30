<?php

// app/Services/ProductService.php
namespace App\Services\StafAdmin;

use App\Models\LotBahanBaku;
use App\Models\RestokBahanBaku;
use DateTime;

class RestokBahanBakuService
{
    static public function create(array $data){
        $restokBahanBaku = new RestokBahanBaku();
        $lotBahanBaku = new LotBahanBaku();
        try {
            $restok = new DateTime($data['tanggal_restok']);
            $restok = $restok->format('Y-m-d H:i:s');

            $restokBahanBaku->master_bahan_baku_id = $data['bahan_baku_id'];
            $restokBahanBaku->jumlah_restok = $data['jumlah_restok'];
            $restokBahanBaku->tanggal_restok = $restok;
            $restokBahanBaku->save();

            $kadaluarsa = new DateTime($data['tanggal_kadaluarsa']);
            $kadaluarsa = $kadaluarsa->format('Y-m-d H:i:s');

            $lotBahanBaku->restok_bahan_baku_id = $restokBahanBaku->id;
            $lotBahanBaku->status = 'tersedia';
            $lotBahanBaku->tanggal_kadaluarsa = $kadaluarsa;
            $lotBahanBaku->jumlah = $restokBahanBaku->jumlah_restok;
            $lotBahanBaku->satuan = $data['satuan'];
            $lotBahanBaku->save();
            return true;
        } catch (\Throwable $th) {
            dd($th);
            return false;
        }
    }

    static public function update(array $data, RestokBahanBaku $model){
        try {
            $restok = new DateTime($data['tanggal_restok']);
            $restok = $restok->format('Y-m-d H:i:s');

            if($model->status === 'pending'){
                $model->master_bahan_baku_id = $data['bahan_baku_id'];
                $model->jumlah_restok = $data['jumlah_restok'];
            }
            $model->tanggal_restok = $restok;
            $model->save();

            $kadaluarsa = new DateTime($data['tanggal_kadaluarsa']);
            $kadaluarsa = $kadaluarsa->format('Y-m-d H:i:s');

            $lotBahanBaku = $model->lot_bahan_baku;

            $lotBahanBaku->tanggal_kadaluarsa = $kadaluarsa;
            if($model->status === 'pending') {
                $lotBahanBaku->jumlah = $model->jumlah_restok;
                $lotBahanBaku->satuan = $data['satuan'];
            }
            $lotBahanBaku->save();
            return true;
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    static public function delete(RestokBahanBaku $model){
        try {
            $model->is_archived = true;
            $model->save();
            
            $model->lot_bahan_baku->status = 'archived';
            $model->lot_bahan_baku->is_archived = true;
            $model->lot_bahan_baku->save();
            return true;
        } catch (\Throwable $th) {
            dd($th);
            return false;
        }
    }
}