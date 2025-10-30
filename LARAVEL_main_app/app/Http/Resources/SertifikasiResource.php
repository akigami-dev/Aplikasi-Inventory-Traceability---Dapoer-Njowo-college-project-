<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class SertifikasiResource extends JsonResource
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
            'nama_sertifikasi' => $this->nama_sertifikasi,
            'badan_penerbit' => $this->badan_penerbit,
            'kode_sertifikasi' => $this->kode_sertifikasi,
            'logo_path' => Storage::url("images/sertifikasi/{$this->logo_path}"),
        ];
    }
}
