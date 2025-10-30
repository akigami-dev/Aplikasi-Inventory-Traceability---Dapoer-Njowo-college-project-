<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\LokasiPenyimpananRequest;
use App\Http\Requests\Owner\LokasiPenyimpananRequest_UPDATE;
use App\Models\LokasiPenyimpanan;
use App\Services\Owner\LokasiPenyimpananService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Severity;

class LokasiPenyimpananController extends Controller
{
    protected $lokasiPenyimpananService;

    public function __construct(LokasiPenyimpananService $lokasiPenyimpananService)
    {
        $this->lokasiPenyimpananService = $lokasiPenyimpananService;
    }

    public function index(Request $request)
    {
        $search = $request->s ?? '';
        $paginate = $request->paginate ?? 10;
        $filter = $request->filter ?? [];
        $data = $this->lokasiPenyimpananService->index($search, $paginate, $filter);
        return Inertia::render('owner/LokasiPenyimpanan', [
            'title' => 'Lokasi Penyimpanan',
            'DataLokasiPenyimpanan' => $data,
            'search' => $search,
            'paginate' => $paginate,
            'filter' => $filter
        ]);
    }
    
    public function update(LokasiPenyimpananRequest_UPDATE $request, LokasiPenyimpanan $penyimpanan)
    {
        $data = $this->lokasiPenyimpananService->update($request->validated(), $penyimpanan);
        if($data) {
            return back()->with('toast', toast(Severity::Success, 'Update Lokasi Penyimpanan', 'Update Lokasi Penyimpanan Successfully'));
        }
        return back()->with('toast', toast(Severity::Error, 'Update Lokasi Penyimpanan', 'Update Lokasi Penyimpanan Failed'));
    }

    public function create(LokasiPenyimpananRequest $request)
    {
        $data = $this->lokasiPenyimpananService->create($request->validated());
        if($data) {
            return back()->with('toast', toast(Severity::Success, 'Create Lokasi Penyimpanan', 'Create Lokasi Penyimpanan Successfully'));
        }
        return back()->with('toast', toast(Severity::Error, 'Create Lokasi Penyimpanan', 'Create Lokasi Penyimpanan Failed'));
    }

    public function destroy(LokasiPenyimpanan $penyimpanan)
    {
        $data = $this->lokasiPenyimpananService->destroy($penyimpanan);
        if($data) {
            return back()->with('toast', toast(Severity::Success, 'Delete Lokasi Penyimpanan', 'Delete Lokasi Penyimpanan Successfully'));
        }
        return back()->with('toast', toast(Severity::Error, 'Delete Lokasi Penyimpanan', 'Delete Lokasi Penyimpanan Failed'));
    }
}
