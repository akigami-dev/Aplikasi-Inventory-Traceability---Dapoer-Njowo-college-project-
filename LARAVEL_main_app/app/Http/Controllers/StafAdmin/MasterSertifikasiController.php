<?php

namespace App\Http\Controllers\StafAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StafAdmin\CreateMasterSertifikasiRequest;
use App\Http\Requests\StafAdmin\UpdateMasterSertifikasiRequest;
use App\Http\Resources\SertifikasiResource;
use App\Models\Sertifikasi;
use App\Services\MasterSertifikasiService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Severity;

class MasterSertifikasiController extends Controller
{
    protected $masterSertifikasiService;

    public function __construct(MasterSertifikasiService $masterSertifikasiService)
    {
        $this->masterSertifikasiService = $masterSertifikasiService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->s ?? '';
        $paginate = $request->paginate ?? 10;
        $filter = $request->filter ?? [];
        $sertifikasi = Sertifikasi::getAll($search, $paginate, $filter);
        $sertifikasi = SertifikasiResource::collection($sertifikasi);
        return Inertia::render('stafAdmin/MasterSertifikasi', [
            'title' => "Master Sertifikasi",
            'sertifikasi' => $sertifikasi,
            'search' => $search,
            'paginate' => (int) $paginate,
            'filter' => $filter
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(CreateMasterSertifikasiRequest $request)
    {
        $result = $this->masterSertifikasiService->createSertifikasi($request->validated());
        if($result) {
            return back()->with('toast', toast(Severity::Success, 'Create Sertifikasi', 'Create Sertifikasi Successfully'));
        }
        return back()->with('toast', toast(Severity::Error, 'Create Sertifikasi', 'Create Sertifikasi Failed'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMasterSertifikasiRequest $request, Sertifikasi $sertifikasi)
    {
        $result = $this->masterSertifikasiService->updateSertifikasi($request->validated(), $sertifikasi);
        if($result) {
            return back()->with('toast', toast(Severity::Success, 'Update Sertifikasi', 'Update Sertifikasi Successfully'));
        }
        return back()->with('toast', toast(Severity::Error, 'Update Sertifikasi', 'Update Sertifikasi Failed'));
    }

    public function destroy(Sertifikasi $sertifikasi)
    {
        $isUsed = $sertifikasi->sertifikasiProduks->contains(function ($sertifikasiProduk) {
            return $sertifikasiProduk->masterProduk->is_archived == false;
        });

        if ($isUsed) {
            return back()->with('toast', toast(Severity::Error, 'Delete Sertifikasi', 'Sertifikasi masih digunakan di Produk'));
        }

        $result = $this->masterSertifikasiService->deleteSertifikasi($sertifikasi);
        if($result) {
            return back()->with('toast', toast(Severity::Success, 'Delete Sertifikasi', 'Delete Sertifikasi Successfully'));
        }
        return back()->with('toast', toast(Severity::Error, 'Delete Sertifikasi', 'Delete Sertifikasi Failed'));
    }
}
