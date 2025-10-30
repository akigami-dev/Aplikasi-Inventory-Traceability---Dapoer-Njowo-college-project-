<?php

namespace App\Http\Controllers\StafAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StafAdmin\CreateMasterSupplierRequest;
use App\Http\Requests\StafAdmin\UpdateMasterSupplierRequest;
use App\Http\Resources\SupplierResource;
use App\Models\Supplier;
use App\Services\StafAdmin\MasterSupplierService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Severity;

class MasterSupplierController extends Controller
{
    protected $masterSupplierService;

    public function __construct(MasterSupplierService $masterSupplierService)
    {
        $this->masterSupplierService = $masterSupplierService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->s ?? '';
        $paginate = $request->paginate ?? 10;
        $filter = $request->filter ?? [];
        $supplier = Supplier::getAll($search, $paginate, $filter);
        $supplier = SupplierResource::collection($supplier);
        return Inertia::render('stafAdmin/MasterSupplier', [
            'title' => "Master Supplier",
            'supplier' => $supplier,
            'search' => $search,
            'paginate' => (int) $paginate,
            'filter' => $filter
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(CreateMasterSupplierRequest $request)
    {
        $result = $this->masterSupplierService->createSupplier($request->validated());
        if($result) {
            return back()->with('toast', toast(Severity::Success, 'Create Supplier', 'Create Supplier Successfully'));
        }
        return back()->with('toast', toast(Severity::Error, 'Create Supplier', 'Create Supplier Failed'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMasterSupplierRequest $request, Supplier $supplier)
    {
        $result = $this->masterSupplierService->updateSupplier($request->validated(), $supplier);
        if($result) {
            return back()->with('toast', toast(Severity::Success, 'Update Supplier', 'Update Supplier Successfully'));
        }
        return back()->with('toast', toast(Severity::Error, 'Update Supplier', 'Update Supplier Failed'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        $result = $this->masterSupplierService->deleteSupplier($supplier);
        if($result) {
            return back()->with('toast', toast(Severity::Success, 'Delete Supplier', 'Delete Supplier Successfully'));
        }
        return back()->with('toast', toast(Severity::Error, 'Delete Supplier', 'Delete Supplier Failed'));
    }
}
