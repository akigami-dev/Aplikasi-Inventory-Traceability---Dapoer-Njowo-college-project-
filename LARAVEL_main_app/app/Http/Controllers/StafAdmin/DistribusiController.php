<?php

namespace App\Http\Controllers\StafAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StafAdmin\CreateDistribusiRequest;
use App\Http\Requests\StafAdmin\UpdateDistribusiRequest;
use App\Http\Resources\DistribusiResource;
use App\Http\Resources\ProduksiResource;
use App\Models\Distribusi;
use App\Models\Produksi;
use App\Services\StafAdmin\DistribusiService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Severity;

class DistribusiController extends Controller
{
    protected $distribusiService;

    public function __construct(DistribusiService $distribusiService)
    {
        $this->distribusiService = $distribusiService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->s ?? '';
        $filter = $request->filter ?? [];
        $paginate = $request->paginate ?? 10;
        $distribusi = $this->distribusiService->index($search, $paginate, $filter);

        $produksi = Produksi::where('is_archived', false)->where('status', '=', 'Selesai')->orderBy('created_at', 'desc');
        $allProduksi = ProduksiResource::collection($produksi->get())->resolve();

        $produksi = $produksi->whereNotIn('id', Distribusi::where('is_archived', false)->pluck('produksi_id'));
        $produksi = ProduksiResource::collection($produksi->get())->resolve();

        return Inertia::render('stafAdmin/Distribusi', [
            'title' => 'Distribusi',
            'distribusi' => $distribusi,
            'produksi' => $produksi,
            'allProduksi' => $allProduksi, // tidak digunakan
            'search' => $search,
            'filter' => $filter,
            'paginate' => $paginate
        ]);
    }

    public function create(CreateDistribusiRequest $request)
    {
        $result = $this->distribusiService->create($request->validated());
        if($result){
            return back()->with('toast', toast(Severity::Success, 'Create Distribusi', 'Create Distribusi Successfully'));
        }
        return back()->with('toast', toast(Severity::Error, 'Create Distribusi', 'Create Distribusi Failed'));
    }

    public function update(UpdateDistribusiRequest $request, Distribusi $distribusi)
    {
        $result = $this->distribusiService->update($request->validated(), $distribusi);
        if($result){
            return back()->with('toast', toast(Severity::Success, 'Update Distribusi', 'Update Distribusi Successfully'));
        }
        return back()->with('toast', toast(Severity::Error, 'Update Distribusi', 'Update Distribusi Failed'));
    }

    public function destroy(Distribusi $distribusi)
    {
        $result = $this->distribusiService->delete($distribusi);
        if($result){
            return back()->with('toast', toast(Severity::Success, 'Delete Distribusi', 'Delete Distribusi Successfully'));
        }
        return back()->with('toast', toast(Severity::Error, 'Delete Distribusi', 'Delete Distribusi Failed'));
    }
}
