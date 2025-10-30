<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MasterBahanBakuResource extends JsonResource
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
            'kode_bahan' => $this->kode_bahan,
            'nama_bahan' => $this->nama_bahan,
            'supplier' => [
                'id' => $this->supplier->id,
                'kode_supplier' => $this->supplier->kode_supplier,
                'nama_supplier' => $this->supplier->nama_supplier,
            ],
        ];
    }
}
