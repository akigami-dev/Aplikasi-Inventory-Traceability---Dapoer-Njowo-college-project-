<?php

// app/Services/ProductService.php
namespace App\Services\Owner;

use App\Http\Resources\KategoriResource;
use App\Models\MasterKategori;

class KategoriService
{
    public function index(string $search, int $paginate = 10, array $filter = []){
        try {
            $data = MasterKategori::getAll($search, $paginate, $filter);
            // dd($data);
            $data = KategoriResource::collection($data);
            return $data;
        } catch (\Throwable $th) {
            dd($th);
        }
    }


    public function create(array $data){
        try {
            $kategori = new MasterKategori();
            $kategori->name = $data['nama_kategori'];
            $kategori->deskripsi = $data['deskripsi'];
            $kategori->save();
            return true;
        } catch (\Throwable $th) {
            dd($th);
        }     
    }

    public function update(array $data, MasterKategori $kategori){
        try {
            $kategori->name = $data['nama_kategori'];
            $kategori->deskripsi = $data['deskripsi'];
            $kategori->save();
            return true;
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public function delete(MasterKategori $kategori){
        try {
            $kategori->delete();
            return true;
        } catch (\Throwable $th) {
            dd($th);
        }
    }
}