<?php

namespace App\Http\Controllers;

use App\Services\Owner\LaporanService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LaporanController extends Controller
{
    protected $laporanService;

    public function __construct(LaporanService $laporanService)
    {
        $this->laporanService = $laporanService;
    }

    public function index(Request $request)
    {
        $laporan = lowercase($request->laporan) ?? '';
        if($laporan !== ''){
            switch ($laporan) {
                case 'produksi':
                    return $this->laporanProduksi($request);
                    break;
                case 'restok bahan baku':
                    return $this->laporanRestokBahanBaku($request);
                    break;
                case 'lot bahan baku':
                    return $this->laporanLotBahanBaku($request);
                    break;
                case 'distribusi':
                    return $this->laporanDistribusi($request);
                    break;
                case 'recall produk':
                    return $this->laporanRecallProduk($request);
                    break;
            }
            abort(404);
        }
        return Inertia::render('owner/Laporan', [
            'title' => 'Laporan'
        ]);
    }

    public function laporanProduksi(Request $request)
    {
        $filter = $request->filter ?? [];
        $search = $request->s ?? '';
        $laporan = $request->laporan ?? null;
        $paginate = $request->paginate ?? 10;
        $result = $this->laporanService->indexProduksi($search, $paginate, $filter);
        return Inertia::render('owner/LaporanProduksi', [
            'title' => 'Laporan Produksi',
            'laporan' => capitalized($laporan),
            'search' => $search,
            'dataLaporan' => $result,
            'paginate' => (int) $paginate,
            'filter' => $filter,
        ]);
    }

    public function laporanRestokBahanBaku(Request $request)
    {
        $filter = $request->filter ?? [];
        $search = $request->s ?? '';
        $laporan = $request->laporan ?? null;
        $paginate = $request->paginate ?? 10;
        $result = $this->laporanService->indexRestok($search, $paginate, $filter);
        return Inertia::render('owner/LaporanRestokBahanBaku', [
            'title' => 'Laporan Restok Bahan Baku',
            'laporan' => capitalized($laporan),
            'search' => $search,
            'dataLaporan' => $result,
            'paginate' => (int) $paginate,
            'filter' => $filter
        ]);
    }

    public function laporanLotBahanBaku(Request $request)
    {
        $search = $request->s ?? '';
        $filter = $request->filter ?? [];
        $laporan = $request->laporan ?? null;
        $paginate = $request->paginate ?? 10;
        $result = $this->laporanService->indexLot($search, $paginate, $filter);
        return Inertia::render('owner/LaporanLotBahanBaku', [
            'title' => 'Laporan Lot Bahan Baku',
            'laporan' => capitalized($laporan),
            'search' => $search,
            'dataLaporan' => $result,
            'paginate' => (int) $paginate,
            'filter' => $filter
        ]);
    }

    public function laporanDistribusi(Request $request)
    {
        $search = $request->s ?? '';
        $filter = $request->filter ?? [];
        $laporan = $request->laporan ?? null;
        $paginate = $request->paginate ?? 10;
        $result = $this->laporanService->indexDistribusi($search, $paginate, $filter);
        return Inertia::render('owner/LaporanDistribusi', [
            'title' => 'Laporan Distribusi',
            'laporan' => capitalized($laporan),
            'search' => $search,
            'dataLaporan' => $result,
            'paginate' => (int) $paginate,
            'filter' => $filter
        ]);
    }

    public function laporanRecallProduk(Request $request)
    {
        $search = $request->s ?? '';
        $filter = $request->filter ?? [];
        $laporan = $request->laporan ?? null;
        $paginate = $request->paginate ?? 10;
        $result = $this->laporanService->indexRecallProduk($search, $paginate, $filter);
        return Inertia::render('owner/LaporanRecallProduk', [
            'title' => 'Laporan Recall Produk',
            'laporan' => capitalized($laporan),
            'search' => $search,
            'dataLaporan' => $result,
            'paginate' => (int) $paginate,
            'filter' => $filter
        ]);
    }


    public function export_pdf(Request $request)
    {
        $laporan = lowercase($request->laporan) ?? '';
        if($laporan !== ''){
            switch ($laporan) {
                case 'produksi':
                    return $this->laporanProduksi_exportPDF($request);
                    break;
                case 'restok bahan baku':
                    return $this->laporanRestokBahanBaku_exportPDF($request);
                    break;
                case 'lot bahan baku':
                    return $this->laporanLotBahanBaku_exportPDF($request);
                    break;
                case 'distribusi':
                    return $this->laporanDistribusi_exportPDF($request);
                    break;
                case 'recall produk':
                    return $this->laporanRecallProduk_exportPDF($request);
                    break;
                case 'detail_lot':
                    return $this->laporanDetailLot_exportPDF($request);
                    break;
                case 'detail_distribusi':
                    return $this->laporanDetailDistribusi_exportPDF($request);
                    break;
                case 'detail_produksi':
                    return $this->laporanDetailProduksi_exportPDF($request);
                    break;
            }
            abort(404);
        }
        return Inertia::render('owner/Laporan', [
            'title' => 'Laporan'
        ]);
    }
    
    public function laporanProduksi_exportPDF(Request $request)
    {
        return Inertia::render('pdfs/laporan/LaporanProduksi', [
            'title' => 'Export PDF Laporan Produksi',
            'dataLaporan' => $request->dataLaporan,
        ]);
        // return Inertia::render('pdfs/test_jspdf', [
        //     'title' => 'Export PDF Laporan Produksi',
        // ]);
    }
    public function laporanRestokBahanBaku_exportPDF(Request $request)
    {
        return Inertia::render('pdfs/laporan/LaporanRestokBahanBaku', [
            'title' => 'Export PDF Laporan Restok Bahan Baku',
            'dataLaporan' => $request->dataLaporan,
        ]);
    }
    public function laporanLotBahanBaku_exportPDF(Request $request)
    {
        return Inertia::render('pdfs/laporan/LaporanLotBahanBaku', [
            'title' => 'Export PDF Laporan Lot Bahan Baku',
            'dataLaporan' => $request->dataLaporan,
        ]);
    }
    public function laporanDistribusi_exportPDF(Request $request)
    {
        return Inertia::render('pdfs/laporan/LaporanDistribusi', [
            'title' => 'Export PDF Laporan Distribusi',
            'dataLaporan' => $request->dataLaporan,
        ]);
    }
    public function laporanRecallProduk_exportPDF(Request $request)
    {
        return Inertia::render('pdfs/laporan/LaporanRecallProduk', [
            'title' => 'Export PDF Laporan Distribusi',
            'dataLaporan' => $request->dataLaporan,
        ]);
    }

    public function laporanDetailLot_exportPDF(Request $request)
    {
        return Inertia::render('pdfs/laporan/LaporanDetailLot', [
            'title' => 'Export PDF Laporan Detail Lot',
            'dataLaporan' => $request->dataLaporan,
        ]);
    }

    public function laporanDetailDistribusi_exportPDF(Request $request)
    {
        return Inertia::render('pdfs/laporan/LaporanDetailDistribusi', [
            'title' => 'Export PDF Laporan Detail Distribusi',
            'dataLaporan' => $request->dataLaporan,
        ]);
    }

    public function laporanDetailProduksi_exportPDF(Request $request)
    {
        return Inertia::render('pdfs/laporan/LaporanDetailProduksi', [
            'title' => 'Export PDF Laporan Detail Produksi',
            'dataLaporan' => $request->dataLaporan,
        ]);
    }
}
