<?php

namespace App\Http\Resources;

use App\Models\Distribusi;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProduksiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $lokasiPenyimpanan = (new LokasiPenyimpananResource($this->lokasi_penyimpanan))->resolve();
        $produkImageFolder = config('constants.storage_product_image_path');
        $produksiImageFolder = config('constants.storage_produksi_image_path');
        $produk = new MasterProdukResource($this->master_produk);
        $distCheck = Distribusi::where('produksi_id', $this->id)->first();
        $distCheck = $distCheck ? true : false;
        return [
            'id' => $this->id,
            'produk' => $produk->resolve(),
            'nomor_batch' => $this->nomor_batch,
            'kuantitas' => $this->kuantitas,
            'tanggal_kadaluarsa' => $this->tanggal_kadaluarsa,
            'status' => $this->status,
            'lokasi_penyimpanan' => $lokasiPenyimpanan,
            'suhu_penyimpanan' => $this->suhu_penyimpanan,
            'mulai_produksi' => $this->mulai_produksi,
            'selesai_produksi' => $this->selesai_produksi,
            'qrcode_path' => imageURL($produksiImageFolder, $this->qrcode_path),
            'label_path' => imageURL($produksiImageFolder, $this->label_path),
            'user_name' => $this->user->name,
            'bahan_baku' => LotPenggunaanLogsResource::collection($this->lot_penggunaan_logs)->resolve(),
            'distribusi_check' => $distCheck
        ];
        // nomor_batch, bahan baku, kuantitas, tanggal_kadaluarsa, status, mulai_produksi, selesai_produksi, 
    }
}
