<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReturProdukResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = new UserResource($this->user);

        $distribusi = new DistribusiResource($this->distribusi);
        return [
            'id' => $this->id,
            'tanggal_retur' => parseDate($this->tanggal_retur),
            'jumlah_retur' => $this->jumlah_retur,
            'keterangan' => $this->keterangan,
            'tindakan' => $this->tindakan,
            'catatan_tambahan' => $this->catatan_tambahan,
            'user' => $user->resolve(),
            'distribusi' => $distribusi->resolve(),
        ];
    }
}
