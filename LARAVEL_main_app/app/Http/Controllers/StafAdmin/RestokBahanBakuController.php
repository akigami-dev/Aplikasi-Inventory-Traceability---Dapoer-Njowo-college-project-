<?php

namespace App\Http\Controllers\StafAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StafAdmin\CreateRestokBahanBakuRequest;
use App\Http\Resources\MasterBahanBakuResource;
use App\Http\Resources\RestokBahanBakuResource;
use App\Models\MasterBahanBaku;
use App\Models\RestokBahanBaku;
use App\Services\StafAdmin\RestokBahanBakuService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Severity;

class RestokBahanBakuController extends Controller
{
    protected $restokBahanBakuService;
    public function __construct(RestokBahanBakuService $service)
    {
        $this->restokBahanBakuService = $service;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->s ?? '';
        $filter = $request->filter ?? [];
        $paginate = $request->paginate ?? 10;
        $restok = RestokBahanBaku::getAll($search, $paginate, $filter);
        $restok = RestokBahanBakuResource::collection($restok);
        return Inertia::render('stafAdmin/RestokBahanBaku', [
            'title' => 'Restok Bahan Baku', 
            'restok' => $restok,
            'bahan_baku' => MasterBahanBakuResource::collection(MasterBahanBaku::where('is_archived', false)->get())->resolve(),
            'search' => $search,
            'filter' => $filter,
            'paginate' => (int) $paginate
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(CreateRestokBahanBakuRequest $request)
    {
        $result = $this->restokBahanBakuService->create($request->validated());
        if($result) {
            return back()->with('toast', toast(Severity::Success, 'Create Restok Bahan Baku', 'Create Restok Bahan Baku Successfully'));
        }
        return back()->with('toast', toast(Severity::Error, 'Create Restok Bahan Baku', 'Create Restok Bahan Baku Failed'));
    }
    public function update(CreateRestokBahanBakuRequest $request, RestokBahanBaku $restokBahanBaku)
    {
        $result = $this->restokBahanBakuService->update($request->validated(), $restokBahanBaku);
        if($result) {
            return back()->with('toast', toast(Severity::Success, 'Update Restok Bahan Baku', 'Update Restok Bahan Baku Successfully'));
        }
        return back()->with('toast', toast(Severity::Error, 'Update Restok Bahan Baku', 'Update Restok Bahan Baku Failed'));
    }
    public function destroy(RestokBahanBaku $restokBahanBaku)
    {
        $result = $this->restokBahanBakuService->delete($restokBahanBaku);
        if($result) {
            return back()->with('toast', toast(Severity::Success, 'Delete Restok Bahan Baku', 'Delete Restok Bahan Baku Successfully'));
        }
        return back()->with('toast', toast(Severity::Error, 'Delete Restok Bahan Baku', 'Delete Restok Bahan Baku Failed'));
    }
}
