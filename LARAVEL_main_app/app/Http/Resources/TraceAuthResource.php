<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TraceAuthResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // PRODUKSI
        $prod = new ProduksiResource($this);
        $prod = $prod->resolve();
        $produksi = [
            'id' => $prod['id'],
            'nomor_batch' => $prod['nomor_batch'],
            'kuantitas' => $prod['kuantitas'],
            'mulai_produksi' => $prod['mulai_produksi'],
            'selesai_produksi' => $prod['selesai_produksi'],
            'tanggal_kadaluarsa' => $prod['tanggal_kadaluarsa'],
            'qrcode_path' => $prod['qrcode_path'],
            'label_path' => $prod['label_path'],
            'user_name' => $prod['user_name'],
            'lokasi_penyimpanan' => $prod['lokasi_penyimpanan']['name'],
            'suhu_penyimpanan' => $prod['suhu_penyimpanan'],
        ];
        
        // PRODUK
        $produk = $prod['produk'];

        // BAHAN BAKU
        $bahan_baku_raw = $prod['bahan_baku'];
        $bahan_baku = [];
        foreach($bahan_baku_raw as $bk){
            $bahan_baku[] = [
                'id' => $bk['id'],
                'satuan' => $bk['satuan'],
                'jumlah_penggunaan' => $bk['jumlah_penggunaan'],
                'user_name' => $bk['user_name'],
                'lot_bahan_baku' => [
                    'id' => $bk['lot_bahan_baku']['id'],
                    'kode_lot' => $bk['lot_bahan_baku']['kode_lot'],
                    'tanggal_kadaluarsa' => $bk['lot_bahan_baku']['tanggal_kadaluarsa'],
                    'jumlah' => $bk['lot_bahan_baku']['jumlah'],
                    'satuan' => $bk['lot_bahan_baku']['satuan'],
                    'restok_bahan_baku' => [
                        'id' => $bk['lot_bahan_baku']['restok_bahan_baku']['id'],
                        'kode_restok' => $bk['lot_bahan_baku']['restok_bahan_baku']['kode_restok'],
                        'jumlah_restok' => $bk['lot_bahan_baku']['restok_bahan_baku']['jumlah_restok'],
                        'tanggal_restok' => $bk['lot_bahan_baku']['restok_bahan_baku']['tanggal_restok'],
                    ]
                ],
                'bahan_baku' => [
                    'id' => $bk['bahan_baku']['id'],
                    'nama_bahan' => $bk['bahan_baku']['nama_bahan'],
                    'kode_bahan' => $bk['bahan_baku']['kode_bahan'],
                    'supplier' => [
                        'id' => $bk['bahan_baku']['supplier']['id'],
                        'nama_supplier' => $bk['bahan_baku']['supplier']['nama_supplier'],
                        'kode_supplier' => $bk['bahan_baku']['supplier']['kode_supplier'],
                    ]
                ]
            ];
        }

        // DISTRIBUSI
        $distribusi = new DistribusiResource($this->distribusi);
        $distribusi = $distribusi->resolve();
        unset($distribusi['produksi']);
        
        $search = $request->s ?? '';

        return[
            'produksi' => $produksi,
            'produk' => $produk,
            'bahan_baku' => $bahan_baku,
            'distribusi' => $distribusi,
        ];
    }
}
