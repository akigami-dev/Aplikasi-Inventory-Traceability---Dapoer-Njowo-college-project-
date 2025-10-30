<?php

namespace App\Http\Resources;

use App\Models\MasterKategori;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class MasterProdukResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // dd(SertifikasiResource::collection($this->sertifikasi)->resolve());
        $kategori = $this->produkKategori->whereNull('deleted_at')->first()->masterKategori()->withTrashed()->first();
        $kategori = (new KategoriResource($kategori))->resolve();
        // dd($kategori);
        return [
            'id' => $this->id,
            'kode_produk' => $this->kode_produk,
            'nama_produk' => $this->nama_produk,
            'gambar' => Storage::url("images/master_produk/{$this->gambar_path}"),
            'template_label' => Storage::url("images/master_produk/{$this->template_label_path}"),
            'kategori' => $kategori, 
            'berat_bersih' => $this->berat_bersih,
            'satuan' => $this->satuan_berat,
            'harga' => $this->harga,
            'sertifikasi' => SertifikasiProdukResource::collection($this->sertifikasiProduk)->resolve(),
        ];
    }
}
