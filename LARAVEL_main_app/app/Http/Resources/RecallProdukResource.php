<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecallProdukResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $distribusi = (new DistribusiResource($this->distribusi))->resolve();
        $user = new UserResource($this->user);
        return [
            'id' => $this->id,
            'distribusi' => $distribusi,
            'kode_recall' => $this->kode_recall,
            'tanggal_recall' => $this->tanggal_recall,
            'jumlah_recall' => $this->jumlah_recall,
            'alasan_recall' => $this->alasan_recall,
            'keterangan' => $this->keterangan,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'role' => $user->role->nama_role
            ],
        ];
    }
}
