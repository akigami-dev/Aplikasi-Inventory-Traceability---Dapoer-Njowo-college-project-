<?php

// app/Services/ProductService.php
namespace App\Services;

use App\Models\Sertifikasi;
use Illuminate\Support\Facades\Storage;
use Severity;

class MasterSertifikasiService
{
    static public function createSertifikasi(array $data)
    {
        $sertifikasi = new Sertifikasi();

        try {
            // how to check if file is uploaded
            if ($data['logo']) {
                $latest_id = Sertifikasi::latest('id')->first();
                $latest_id = $latest_id ? $latest_id->id + 1 : 1;

                $imagePath = config('constants.storage_sertifikasi_image_path');
                $logo = $data['logo'];
                $imageName = time() . "_{$latest_id}_sertifikasi." . $logo->getClientOriginalExtension();
                $logo->storeAs($imagePath, $imageName, 'public');
                $sertifikasi->logo_path = $imageName;
            }

            $sertifikasi->nama_sertifikasi = $data['nama_sertifikasi'];
            $sertifikasi->badan_penerbit = $data['badan_penerbit'];
            $sertifikasi->kode_sertifikasi = $data['kode_sertifikasi'];
            $sertifikasi->save();
            
            return $sertifikasi;
        } catch (\Throwable $th) {
            dd($th);
            return false;
        }
    }

    static public function updateSertifikasi(array $data, Sertifikasi $sertifikasi)
    {
        try {
            if ($data['logo']) {
                $imagePath = config('constants.storage_sertifikasi_image_path');

                $logo = $data['logo'];
                $imageName = time() . "_{$sertifikasi->id}_sertifikasi." . $logo->getClientOriginalExtension();
                $logo->storeAs($imagePath, $imageName, 'public');

                deleteImage($imagePath, $sertifikasi->logo_path);
                $sertifikasi->logo_path = $imageName;
            }
            $sertifikasi->nama_sertifikasi = $data['nama_sertifikasi'];
            $sertifikasi->badan_penerbit = $data['badan_penerbit'];
            $sertifikasi->kode_sertifikasi = $data['kode_sertifikasi'];
            $sertifikasi->save();
            return $sertifikasi;
        } catch (\Throwable $th) {
            return false;
        }
    }

    static public function deleteSertifikasi(Sertifikasi $sertifikasi)
    {
        try {
            $imagePath = config('constants.storage_sertifikasi_image_path');
            // deleteImage($imagePath, $sertifikasi->logo_path);
            $sertifikasi->delete();
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }
}
