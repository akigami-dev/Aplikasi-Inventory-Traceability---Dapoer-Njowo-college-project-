<?php

// app/Services/ProductService.php
namespace App\Services\StafAdmin;

use App\Models\MasterBahanBaku;

class MasterBahanBakuService
{
    static public function create(array $data){
        $bahanBaku = new MasterBahanBaku();

        try {
            $bahanBaku->nama_bahan = $data['nama_bahan'];
            $bahanBaku->supplier_id = (int)$data['supplier'];
            $bahanBaku->save();
            return true;
        } catch (\Throwable $th) {
            dd($th);
            return false;
        }
    }

    static public function update(array $data, MasterBahanBaku $masterBahanBaku){
        try {
            $masterBahanBaku->nama_bahan = $data['nama_bahan'];
            $masterBahanBaku->supplier_id = (int)$data['supplier'];
            $masterBahanBaku->save();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    static public function destroy(MasterBahanBaku $masterBahanBaku){
        try {
            $masterBahanBaku->is_archived = true;
            $masterBahanBaku->save();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
}