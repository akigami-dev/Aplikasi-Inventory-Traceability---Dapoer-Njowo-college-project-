<?php

// app/Services/ProductService.php
namespace App\Services\StafProduksi;

use App\Http\Resources\ProduksiResource;
use App\Models\LotBahanBaku;
use App\Models\LotPenggunaanLogs;
use App\Models\Produksi;
use App\Services\LotPenggunaanLogsService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ProduksiService
{
    protected $lotPenggunanLogsService;

    public function __construct(LotPenggunaanLogsService $lotPenggunanLogsService)
    {
        $this->lotPenggunanLogsService = $lotPenggunanLogsService;
    }

    public function getAllProduksi(String $search = '', int $paginate = 10, array $filter = []){
        try {
            $produksi = Produksi::with([
               'master_produk', 
            ]);
            $produksi = $produksi->where('is_archived', false);
            $produksi = $produksi->where(function (Builder $query) use($search) {
               $query
               ->where('nomor_batch', 'like', '%'.$search.'%')
               ->orWhere('status', '=', $search)
               ->orWhere('kuantitas', '=', $search)
               ->orWhere(function (Builder $qdate) use ($search) {
                    $qdate->whereYear('mulai_produksi', $search)
                        ->orWhereMonth('mulai_produksi', $search)
                        ->orWhereDay('mulai_produksi', $search)
                        ->orWhereDate('mulai_produksi', $search)
                        ->orWhereTime('mulai_produksi', '>', $search);
                })
                ->orWhere(function (Builder $qdate) use ($search) {
                    $qdate->whereYear('selesai_produksi', $search)
                        ->orWhereMonth('selesai_produksi', $search)
                        ->orWhereDay('selesai_produksi', $search)
                        ->orWhereDate('selesai_produksi', $search)
                        ->orWhereTime('selesai_produksi', '>', $search);
                })
                ->orWhere(function (Builder $qdate) use ($search) {
                    $qdate->whereYear('tanggal_kadaluarsa', $search)
                        ->orWhereMonth('tanggal_kadaluarsa', $search)
                        ->orWhereDay('tanggal_kadaluarsa', $search)
                        ->orWhereDate('tanggal_kadaluarsa', $search);
                })
                ->orWhereHas('master_produk', function (Builder $qmp) use($search) {
                   $qmp
                   ->where('nama_produk', 'like', '%'.$search.'%')
                   ->orWhere('kode_produk', 'like', '%'.$search.'%');
                });
            });

            // FILTERS
            if(isset($filter['sort'])){
                $sort = $filter['sort'];
                $field = Produksi::getField($sort['field']);
                $order = $sort['order'];
                if($field){
                    $produksi = $produksi->orderBy($field, $order);
                }
            }else{
                $produksi = $produksi->orderBy('created_at', 'desc');
            }
            
            if(isset($filter['range'])){
                $range = $filter['range'];
                $start = Carbon::parse($range['time'][0])->startOfDay();
                $end = Carbon::parse($range['time'][1])->endOfDay();
                $field = Produksi::getField($range['field']);
                if($field){
                    $produksi = $produksi->whereBetween($field, [$start, $end]);
                }
            }

            $produksi = $produksi->paginate($paginate);
            $result = ProduksiResource::collection($produksi);
            return $result;
        } catch (\Throwable $th) {
            dd($th);
            return false;
        }
    }
    
    public function index(String $search, int $paginate, array $filter = [])
    {
        $dataProduksi = $this->getAllProduksi($search, $paginate, $filter);
        

        return $dataProduksi;
    }

    public function create(array $data){
        $produksi = new Produksi();

        try {
            $produksi->master_produk_id = $data['produk_id'];
            $produksi->kuantitas = $data['kuantitas'];
            $produksi->lokasi_penyimpanan_id = $data['lokasi_penyimpanan_id'];
            $produksi->suhu_penyimpanan = $data['suhu_penyimpanan'];
            $produksi->user_id = Auth::id();
            $produksi->save();

            $this->lotPenggunanLogsService->addPenggunaan($data['bahan_baku'], $produksi->id);
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function update(array $data, Produksi $produksi){
        try {
            $this->lotPenggunanLogsService->editPenggunaan($data['bahan_baku'], $produksi);
            $produksi->master_produk_id = $data['produk_id'];
            $produksi->kuantitas = $data['kuantitas'];
            $produksi->lokasi_penyimpanan_id = $data['lokasi_penyimpanan_id'];
            $produksi->suhu_penyimpanan = $data['suhu_penyimpanan'];
            $produksi->user_id = Auth::id();
            $produksi->save();

            return true;
        } catch (\Throwable $th) {
            dd($th);
            return false;
        }
    }

    static public function delete(Produksi $produksi){
        try {
            // dd($produksi);
            // $produksi->lot_penggunaan_logs->each->delete();
            $produksi->is_archived = true;
            $produksi->save();
            return true;
            
        } catch (\Throwable $th) {
            dd($th);
            return false;
        }
    }

    static public function proses(Produksi $produksi){
        try {
            $produksi->status = "Proses";
            $produksi->mulai_produksi = now();
            $produksi->save();
            $produksi->lot_penggunaan_logs->each->update(['tanggal_kadaluarsa' => now()]);
            return true;
        } catch (\Throwable $th) {
            dd($th);
            return false;
        }   
    }
    static public function selesai(array $data, Produksi $produksi){
        try {
            $produksi->status = "Selesai";
            $produksi->selesai_produksi = parseDate($data['selesai_produksi']);
            $produksi->tanggal_kadaluarsa = parseDate($data['tanggal_kadaluarsa']);
            
            $result = self::makeLabel($produksi);
            if($result === false) return false;
            $produksi->label_path = $result['label'];
            $produksi->qrcode_path = $result['qr'];

            $produksi->save();
            return true;
        } catch (\Throwable $th) {
            dd($th);
            return false;
        }   
    }

    static public function edit_kadaluarsa(array $data, Produksi $produksi){
        try {
            if(parseDate($produksi->tanggal_kadaluarsa, 'Y-m-d') == parseDate($data['tanggal_kadaluarsa'], 'Y-m-d')) return back()->withErrors('tanggal_kadaluarsa','Tanggal Kadaluarsa Tidak Berubah');

            $produksi->tanggal_kadaluarsa = parseDate($data['tanggal_kadaluarsa']);
            $result = self::makeLabel($produksi);
            if($result === false) return false;
            
            removeImage(config('constants.storage_produksi_image_path'), $produksi->label_path);
            removeImage(config('constants.storage_produksi_image_path'), $produksi->qrcode_path);
            $produksi->label_path = $result['label'];
            $produksi->qrcode_path = $result['qr'];

            $produksi->save();
            return true;
        } catch (\Throwable $th) {
            dd($th);
            return false;
        }
    }

    static protected function makeLabel(Produksi $produksi){
        $template_label = $produksi->master_produk->template_label_path;
        $imageContent = getStorageImage(config('constants.storage_product_image_path'), $template_label);
        $bahanBaku = [];

        foreach($produksi->lot_penggunaan_logs as $value){
            $bahanBaku[] = [
                    'nama_bahan' => $value->lot_bahan_baku->restok_bahan_baku->master_bahan_baku->nama_bahan,
                    'jumlah' => $value->jumlah,
                    'satuan' => $value->lot_bahan_baku->satuan,
                    'nama_supplier' => $value->lot_bahan_baku->restok_bahan_baku->master_bahan_baku->supplier->nama_supplier
            ];
        };
        $response = createLabel($imageContent, $template_label, $produksi->master_produk->nama_produk, $produksi->tanggal_kadaluarsa, $produksi->mulai_produksi, $produksi->nomor_batch, $bahanBaku);
        if($response->successful()){
            $produksiStorage = config('constants.storage_produksi_image_path');
            $dataResponse = $response->json();
            $leading = time() ."_{$produksi->nomor_batch}_{$produksi->id}";
            $labelName = "{$leading}_label.jpg";
            $qrName = "{$leading}_qr.jpg";
            Storage::disk('public')->put("{$produksiStorage}/" . $labelName, base64_decode($dataResponse['label']));
            Storage::disk('public')->put("{$produksiStorage}/" . $qrName, base64_decode($dataResponse['qr']));
            return [
                'qr' => $qrName,
                'label' => $labelName
            ];
        }else{
            return false;
        }
    }
}