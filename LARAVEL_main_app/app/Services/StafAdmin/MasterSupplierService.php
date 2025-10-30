<?php

// app/Services/ProductService.php
namespace App\Services\StafAdmin;

use App\Models\MasterBahanBaku;
use App\Models\Sertifikasi;
use App\Models\StokBahanBaku;
use App\Models\Supplier;

class MasterSupplierService
{
    static public function createSupplier(array $data){
        $supplier = new Supplier();

        try {
            $supplier->nama_supplier = $data['nama_supplier'];
            $supplier->email = $data['email'];
            $supplier->no_telpon = $data['no_telp'];
            $supplier->alamat = $data['alamat'];
            $supplier->save();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    static public function updateSupplier(array $data, Supplier $supplier){
        try {
            $supplier->nama_supplier = $data['nama_supplier'];
            $supplier->email = $data['email'];
            $supplier->no_telpon = $data['no_telp'];
            $supplier->alamat = $data['alamat'];
            $supplier->save();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    static public function deleteSupplier(Supplier $supplier){
        try {
            $supplier->is_archived = true;
            $supplier->save();
            // MasterBahanBaku::where('supplier_id', $supplier->id)->update(['is_archived' => true]);
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
}