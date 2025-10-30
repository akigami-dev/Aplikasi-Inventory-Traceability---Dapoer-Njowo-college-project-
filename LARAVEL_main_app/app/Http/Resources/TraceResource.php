<?php

namespace App\Http\Resources;

use App\Models\BiodataUmkm;
use App\Models\Distribusi;
use App\Models\LotPenggunaanLogs;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TraceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // PRODUKSI
        $lokasi_penyimpanan = $this->lokasi_penyimpanan->name;
        $suhu_penyimpanan = $this->suhu_penyimpanan;
        $produk = $this->master_produk;
        $gambar = imageURL(config('constants.storage_product_image_path'), $this->master_produk->gambar_path);
        $qr = imageURL(config('constants.storage_produksi_image_path'), $this->qrcode_path);
        // $kategori = $this->master_produk->kategori;
        $kategori = $this->master_produk->produkKategori->whereNull('deleted_at')->first()->masterKategori()->withTrashed()->first();
        $kategori = (new KategoriResource($kategori))->resolve();
        // dd($kategori);
        $mulai_produksi = parseDate($this->mulai_produksi, 'd M Y');
        $tanggal_kadaluarsa = parseDate($this->tanggal_kadaluarsa, 'd M Y');
        $nomor_batch = $this->nomor_batch;
        $sertifikasi = SertifikasiProdukResource::collection($this->master_produk->sertifikasiProduk);

        // PRODUSEN
        $produsen = BiodataUmkm::first();
        $nama_produsen = $produsen->nama;
        $email = $produsen->email;
        $nomor_telpon = '+62 '. substr($produsen->no_telpon, 1);
        $alamat_produsen = $produsen->alamat;

        // RETAILER
        $distribusi = Distribusi::where('produksi_id', $this->id)->first();
        $nama_retailer = $distribusi->nama_retailer;
        $alamat = $distribusi->alamat;
        $tanggal_pengiriman = parseDate($distribusi->tanggal_pengiriman, 'd M Y');
        $status = 'Terdistribusi';
        // $recallProduk = [];
        // if($distribusi->recall_produk){
        //     if(count($distribusi->recall_produk) > 0){
        //         foreach($distribusi->recall_produk as $recall_produk){
        //             if($recall_produk->is_archived == 1){
        //                 $status = 'Terdistribusi';
        //             }else{
        //                 $recallProduk = [
        //                     'id' => $recall_produk->id,
        //                     'kode_recall' => $recall_produk->kode_recall,
        //                     'tanggal_recall' => $recall_produk->tanggal_recall,
        //                     'jumlah_recall' => $recall_produk->jumlah_recall,
        //                     'alasan_recall' => $recall_produk->alasan_recall,
        //                     'keterangan' => $recall_produk->keterangan,
        //                     'user' => [
        //                         'id' => $recall_produk->user->id,
        //                         'name' => $recall_produk->user->name,
        //                         'role' => $recall_produk->user->role->nama_role
        //                     ],
        //                 ];
        //                 $status = 'Retur';
        //                 break;
        //             }
        //         }
        //     }
        // }

        // dd($recallProduk);

        // BAHAN BAKU
        $bahan_baku = [];
        foreach ($this->lot_penggunaan_logs as $l){
            $bahan_baku[] = [
                'nama_bahan' => $l->lot_bahan_baku->restok_bahan_baku->master_bahan_baku['nama_bahan'],
                'supplier' => $l->lot_bahan_baku->restok_bahan_baku->master_bahan_baku->supplier['nama_supplier'],
                'kode_lot' => $l->lot_bahan_baku['kode_lot'],
                'jumlah' => $l->jumlah,
                'satuan' => $l->lot_bahan_baku['satuan'],
            ];
        }
        return [
            // PRODUKSI
            'produksi' => [
                'nama_produk' => $produk->nama_produk,
                'berat_bersih' => $produk->berat_bersih,
                'satuan_berat' => $produk->satuan_berat,
                'gambar' => $gambar,
                'qr' => $qr,
                'kategori' => $kategori,
                'mulai_produksi' => $mulai_produksi,
                'tanggal_kadaluarsa' => $tanggal_kadaluarsa,
                'nomor_batch' => $nomor_batch,
                'sertifikasi' => $sertifikasi->resolve(),
                'lokasi_penyimpanan' => $lokasi_penyimpanan,
                'suhu_penyimpanan' => $suhu_penyimpanan
            ],

            // PRODUSEN
            'produsen' => [
                'nama_produsen' => $nama_produsen,
                'email' => $email,
                'nomor_telpon' => $nomor_telpon,
                'alamat' => $alamat_produsen
            ],

            // RETAILER
            'retailer' => [
                'nama_retailer' => $nama_retailer,
                'alamat' => $alamat,
                'tanggal_pengiriman' => $tanggal_pengiriman,
                'status' => $status
            ],

            // BAHAN BAKU
            'bahan_baku' => $bahan_baku

            // DISTRIBUSI
        ];
    }
}
