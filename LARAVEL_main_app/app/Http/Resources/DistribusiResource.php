<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DistribusiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Relation: produksi, retur_produk,
        $produksi = $this->produksi;
        $dataProduksi = new ProduksiResource($produksi);
        $dataProduksi = $dataProduksi->resolve();
        $recall_produk = [];
        foreach($this->recall_produk as $rp){
            if($rp->is_archived == 1) continue;
            $recall_produk[] = [
                'id' => $rp->id,
                'kode_recall' => $rp->kode_recall,
                'tanggal_recall' => parseDate($rp->tanggal_recall),
                'jumlah_recall' => $rp->jumlah_recall,
                'alasan_recall' => $rp->alasan_recall,
                'keterangan' => $rp->keterangan,
                'user' => (new UserResource($rp->user))->resolve(),
            ];
        }

        $returProduk = [];
        $status = 'Terdistribusi';
        if($this->retur_produk){
            if(count($this->retur_produk) > 0){
                foreach($this->retur_produk as $retur_produk){
                    if($retur_produk->is_archived == 1){
                        $status = 'Terdistribusi';
                    }else{
                        $returProduk = [
                            'id' => $retur_produk->id,
                            'tanggal_retur' => parseDate($retur_produk->tanggal_retur),
                            'jumlah_retur' => $retur_produk->jumlah_retur,
                            'keterangan' => $retur_produk->keterangan,
                            'tindakan' => $retur_produk->tindakan,
                            'catatan_tambahan' => $retur_produk->catatan_tambahan,
                            'user' => [
                                'id' => $retur_produk->user->id,
                                'name' => $retur_produk->user->name
                            ]
                        ];
                        $status = 'Retur';
                        break;
                    }
                }
            }
        }
        
        return [
            'id' => $this->id,
            'kode_distribusi' => $this->kode_distribusi,
            'nama_retailer' => $this->nama_retailer,
            'alamat' => $this->alamat,
            'tanggal_pengiriman' => $this->tanggal_pengiriman,
            'jumlah_tersisa' => $this->jumlah_tersisa,
            'produksi' => [
                'id' => $dataProduksi['id'],
                'nomor_batch' => $dataProduksi['nomor_batch'],
                'kuantitas' => $dataProduksi['kuantitas'],
                'produk' => $dataProduksi['produk'],
            ],
            'status' => $status,
            'retur_produk' => $returProduk,
            'recall_produk' => $recall_produk,
            'created_at' => parseDate($this->created_at),
            'editable' => parseDate($this->created_at, addDays: 1) < parseDate(now()),
        ];
    }
}
