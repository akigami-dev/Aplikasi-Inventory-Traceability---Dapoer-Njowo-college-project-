<?php

namespace App\Http\Controllers\StafProduksi;

use App\Http\Controllers\Controller;
use App\Http\Requests\StafProduksi\ProduksiRequest;
use App\Http\Requests\StafProduksi\SelesaiProduksiRequest;
use App\Http\Requests\StafProduksi\UpdateProduksiRequest;
use App\Http\Resources\LokasiPenyimpananResource;
use App\Http\Resources\LotBahanBakuResource;
use App\Http\Resources\LotPenggunaanLogsResource;
use App\Http\Resources\MasterProdukResource;
use App\Http\Resources\ProduksiResource;
use App\Models\LokasiPenyimpanan;
use App\Models\LotBahanBaku;
use App\Models\LotPenggunaanLogs;
use App\Models\MasterProduk;
use App\Models\Produksi;
use App\Services\StafProduksi\ProduksiService;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Severity;

class ProduksiController extends Controller
{
    protected $produksiService;

    public function __construct(ProduksiService $produksiService)
    {
        $this->produksiService = $produksiService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // $produksi = Produksi::where("is_archived", false)->first();
        // $produksi = $produksi->lot_penggunaan_logs->first();
        // $produksi = [
        //         [
        //             'nama_bahan' => $produksi->lot_bahan_baku->restok_bahan_baku->master_bahan_baku->nama_bahan,
        //             'jumlah' => $produksi->jumlah,
        //             'satuan' => $produksi->lot_bahan_baku->satuan,
        //             'nama_supplier' => $produksi->lot_bahan_baku->restok_bahan_baku->master_bahan_baku->supplier->nama_supplier
        //         ]
        //     ];

        // $test = '';
        // foreach($produksi as $index => $p){
        //     $test .= $index+1 . '. ' .$p['nama_bahan'] . ': ' . cleanDecimal($p['jumlah']) . ' ' . $p['satuan'] . ' [' . $p['nama_supplier'] .']';
        // }
        // dd($test);
        $search = $request->s ?? '';
        $filter = $request->filter ?? [];
        $paginate = $request->paginate ?? 10;
        $dataProduksi = $this->produksiService->index($search, $paginate, $filter);

        $dataLokasiPenyimpanan = LokasiPenyimpanan::getAll(paginate:0);
        $dataLokasiPenyimpanan = LokasiPenyimpananResource::collection($dataLokasiPenyimpanan)->resolve();
        
        $dataProduk = MasterProduk::where("is_archived", false)->get();
        $dataProduk = MasterProdukResource::collection($dataProduk)->resolve();

        $dataLotBahanBaku = LotBahanBaku::where("is_archived", false)->where('status', 'tersedia')->orderBy('status', 'asc')->orderBy('tanggal_kadaluarsa', 'asc')->get();
        $dataLotBahanBaku = LotBahanBakuResource::collection($dataLotBahanBaku)->resolve();
        return Inertia::render('stafProduksi/Produksi', [
            'title' => 'Produksi',
            'dataProduksi' => $dataProduksi,
            'dataProduk' => $dataProduk,
            'dataLotBahanBaku' => $dataLotBahanBaku,
            'dataLokasiPenyimpanan' => $dataLokasiPenyimpanan,
            'search' => $search,
            'filter' => $filter,
            'paginate' => (int) $paginate,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(ProduksiRequest $request)
    {
        $data = $request->formattedData();
        // dd($data);
        $result = $this->produksiService->create($data);
        if($result) {
            return back()->with('toast', toast(Severity::Success, 'Create Produksi', 'Create Produksi Successfully'));
        }
        return back()->with('toast', toast(Severity::Error, 'Create Produksi', 'Create Produksi Failed'));
    }

    public function proses(Produksi $produksi)
    {
        $result = $this->produksiService->proses($produksi);
        if($result) {
            return back()->with('toast', toast(Severity::Success, 'Update Status Berhasil!', 'Update status produksi berhasil'));
        }
        return back()->with('toast', toast(Severity::Error, 'Update Status Failed!', 'Update status produksi gagal'));
    }
    public function selesai(Produksi $produksi, SelesaiProduksiRequest $request)
    {
        $result = $this->produksiService->selesai($request->validated(), $produksi);
        if($result) {
            return back()->with('toast', toast(Severity::Success, 'Update Status Berhasil!', 'Update status produksi berhasil'));
        }
        return back()->with('toast', toast(Severity::Error, 'Update Status Failed!', 'Update status produksi gagal'));
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProduksiRequest $request, Produksi $produksi)
    {
        // dd($request->all());
        $data = $request->formattedData();
        $result = $this->produksiService->update($data, $produksi);
        if($result) {
            return back()->with('toast', toast(Severity::Success, 'Update Produksi', 'Update Produksi Successfully'));
        }
        return back()->with('toast', toast(Severity::Error, 'Update Produksi', 'Update Produksi Failed'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Produksi $produksi)
    {
        $result = $this->produksiService->delete($produksi);
        if($result) {
            return back()->with('toast', toast(Severity::Success, 'Delete Produksi', 'Delete Produksi Successfully'));
        }
        return back()->with('toast', toast(Severity::Error, 'Delete Produksi', 'Delete Produksi Failed'));
    }

    public function edit_kadaluarsa(Request $request, Produksi $produksi)
    {
        $validated = $request->validate([
            'tanggal_kadaluarsa' => 'required|date',
        ]);
        $result = $this->produksiService->edit_kadaluarsa($validated, $produksi);
        if($result) {
            return back()->with('toast', toast(Severity::Success, 'Delete Produksi', 'Delete Produksi Successfully'));
        }
        return back()->with('toast', toast(Severity::Error, 'Delete Produksi', 'Delete Produksi Failed'));
    }
}
