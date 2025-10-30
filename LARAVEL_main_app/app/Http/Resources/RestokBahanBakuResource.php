<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RestokBahanBakuResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'master_bahan_baku' => MasterBahanBakuResource::make($this->master_bahan_baku)->resolve(),
            'kode_restok' => $this->kode_restok,
            'kode_lot' => $this->lot_bahan_baku->kode_lot,
            'jumlah_restok' => $this->jumlah_restok,
            'tanggal_restok' => $this->tanggal_restok,
            'satuan' => $this->lot_bahan_baku->satuan,
            'tanggal_kadaluarsa' => $this->lot_bahan_baku->tanggal_kadaluarsa,
            'status' => $this->status,
        ];
    }
}
