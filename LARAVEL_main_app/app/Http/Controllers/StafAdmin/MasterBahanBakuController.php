<?php

namespace App\Http\Controllers\StafAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StafAdmin\CreateMasterBahanBakuRequest;
use App\Http\Requests\StafAdmin\UpdateMasterBahanBakuRequest;
use App\Http\Resources\MasterBahanBakuResource;
use App\Models\MasterBahanBaku;
use App\Models\Supplier;
use App\Services\StafAdmin\MasterBahanBakuService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Severity;

class MasterBahanBakuController extends Controller
{
    protected $masterBahanBakuService;
    public function __construct(MasterBahanBakuService $masterBahanBakuService)
    {
        $this->masterBahanBakuService = $masterBahanBakuService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->s ?? '';
        $filter = $request->filter ?? [];
        $paginate = $request->paginate ?? 10;

        $bahanBaku = MasterBahanBaku::getAll($search, $paginate, $filter);
        $bahanBaku = MasterBahanBakuResource::collection($bahanBaku);
        return Inertia::render('stafAdmin/MasterBahanBaku', [
            'title' => 'Master Bahan Baku',
            'bahanBaku' => $bahanBaku,
            'supplier' => Supplier::where('is_archived', false)->get(),
            'search' => $search,
            'paginate' => (int) $paginate,
            'filter' => $filter
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(CreateMasterBahanBakuRequest $request)
    {
        $result = $this->masterBahanBakuService->create($request->validated());
        if($result) {
            return back()->with('toast', toast(Severity::Success, 'Create Bahan Baku', 'Create Bahan Baku Successfully'));
        }
        return back()->with('toast', toast(Severity::Error, 'Create Bahan Baku', 'Create Bahan Baku Failed'));
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMasterBahanBakuRequest $request, MasterBahanBaku $bahan_baku)
    {
        $result = $this->masterBahanBakuService->update($request->validated(), $bahan_baku);
        if($result) {
            return back()->with('toast', toast(Severity::Success, 'Update Bahan Baku', 'Update Bahan Baku Successfully'));
        }
        return back()->with('toast', toast(Severity::Error, 'Update Bahan Baku', 'Update Bahan Baku Failed'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MasterBahanBaku $bahan_baku)
    {
        $result = $this->masterBahanBakuService->destroy($bahan_baku);
        if($result) {
            return back()->with('toast', toast(Severity::Success, 'Delete Bahan Baku', 'Delete Bahan Baku Successfully'));
        }
        return back()->with('toast', toast(Severity::Error, 'Delete Bahan Baku', 'Delete Bahan Baku Failed'));
    }
}
