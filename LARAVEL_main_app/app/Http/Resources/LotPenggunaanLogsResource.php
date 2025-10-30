<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LotPenggunaanLogsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $lot_bahan_baku = new LotBahanBakuResource($this->lot_bahan_baku);
        $lot_bahan_baku = $lot_bahan_baku->resolve();
        $bahan_baku = $lot_bahan_baku['bahan_baku'];
        return [
            'id' => $this->id,
            // 'bahan_baku' => [
            //     'id' => $this->lot_bahan_baku->id,
            //     'kode_bahan' => $this->lot_bahan_baku->restok_bahan_baku->master_bahan_baku->kode_bahan,
            //     'nama_bahan' => $this->lot_bahan_baku->restok_bahan_baku->master_bahan_baku->nama_bahan,
            //     'kode_lot' => $this->lot_bahan_baku->kode_bahan,
            //     'tanggal_kadaluarsa' => $this->lot_bahan_baku->tanggal_kadaluarsa,
            //     'supplier' => [
            //         'nama_supplier' => $this->lot_bahan_baku->restok_bahan_baku->master_bahan_baku->supplier->nama_supplier,
            //         'kode_supplier' => $this->lot_bahan_baku->restok_bahan_baku->master_bahan_baku->supplier->kode_supplier
            //     ],
            // ],
            'kode_lot' => $this->lot_bahan_baku->kode_lot,
            'satuan' => $this->lot_bahan_baku->satuan,
            'jumlah_penggunaan' => $this->jumlah,
            'tanggal_penggunaan' => $this->tanggal_penggunaan,
            'user_name' => $this->user->name,
            'lot_bahan_baku' => $lot_bahan_baku,
            'bahan_baku' => $bahan_baku
        ];
    }
}
