<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class SertifikasiProdukResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $sertifikasi = $this->sertifikasi;
        $sertifikasi = new SertifikasiResource($sertifikasi);
        return [...$sertifikasi->resolve(), 'kode_sertifikasi' => $sertifikasi->kode_sertifikasi];
    }
}
