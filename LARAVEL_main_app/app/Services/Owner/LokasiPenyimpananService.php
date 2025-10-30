<?php

// app/Services/ProductService.php
namespace App\Services\Owner;

use App\Http\Resources\LokasiPenyimpananResource;
use App\Models\LokasiPenyimpanan;

class LokasiPenyimpananService
{
    public function index(string $search, int $paginate, array $filter = [])
    {
        try {
            $data = LokasiPenyimpanan::getAll($search, $paginate, $filter);
            $data = LokasiPenyimpananResource::collection($data);
            return $data;
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public function update(array $data, LokasiPenyimpanan $penyimpanan)
    {
        try {
            $name = $data['nama_lokasi_penyimpanan'];
            $suhu_default = $data['suhu_default'];
            $deskripsi = $data['deskripsi'];
            $data = LokasiPenyimpanan::updateRecord($name, $deskripsi, $suhu_default, $penyimpanan);
            return true;
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public function create(array $data)
    {
        try {
            $name = $data['nama_lokasi_penyimpanan'];
            $suhu_default = $data['suhu_default'];
            $deskripsi = $data['deskripsi'];
            $data = LokasiPenyimpanan::addRecord($name, $deskripsi, $suhu_default);
            return true;
        } catch (\Throwable $th) {
            dd($th);
            return false;
        }
    }

    public function destroy(LokasiPenyimpanan $penyimpanan)
    {
        try {
            $penyimpanan->delete();
            return true;
        } catch (\Throwable $th) {
            dd($th);
            return false;
        }
    }
}