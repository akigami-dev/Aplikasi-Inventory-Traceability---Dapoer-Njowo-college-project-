<?php

namespace App\Http\Resources;

use App\Models\LotPenggunaanLogs;
use App\Models\Produksi;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LotBahanBakuResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $restok_bahan_baku = new RestokBahanBakuResource($this->restok_bahan_baku);
        // $lot_penggunaan_logs = LotPenggunaanLogs::where('lot_bahan_baku_id', $this->id)->orderBy('created_at', 'asc')->get();
        // $data_lot_penggunaan = [];
        // foreach($lot_penggunaan_logs as $lot_penggunaan_log){
        //     $nomor_batch = Produksi::where('id', $lot_penggunaan_log->produksi_id)->where('nomor_batch', '!=', null)->where('is_archived', false)->first();
        //     if(!$nomor_batch) continue;
        //     $data_lot_penggunaan[] = [
        //         'nomor_batch' => $nomor_batch->nomor_batch,
        //         'lot_bahan_baku' => $lot_penggunaan_log->lot_bahan_baku->kode_lot,
        //         'jumlah' => $lot_penggunaan_log->jumlah,
        //         'tanggal_penggunaan' => $lot_penggunaan_log->tanggal_penggunaan,
        //     ];
        // }
        $restok_bahan_baku = $restok_bahan_baku->resolve();
        $data_bahan_baku = $restok_bahan_baku['master_bahan_baku'];
        return [
            'id' => $this->id,
            'kode_lot' => $this->kode_lot,
            'restok_bahan_baku' => $restok_bahan_baku,
            'status' => $this->status,
            'tanggal_kadaluarsa' => $this->tanggal_kadaluarsa,
            'jumlah' => $this->jumlah,
            'satuan' => $this->satuan,
            'bahan_baku' => $data_bahan_baku,
            // 'lot_penggunaan_logs' => $data_lot_penggunaan,
        ];
    }
}
