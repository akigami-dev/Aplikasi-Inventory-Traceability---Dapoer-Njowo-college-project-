<?php

namespace App\Http\Controllers\StafAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StafAdmin\ReturProdukRequest;
use App\Http\Requests\StafAdmin\ReturProdukUpdateRequest;
use App\Http\Resources\DistribusiResource;
use App\Models\Distribusi;
use App\Models\ReturProduk;
use App\Services\StafAdmin\ReturProdukService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Severity;

class ReturProdukController extends Controller
{
    protected $returnProdukService;

    public function __construct(ReturProdukService $returnProdukService)
    {
        $this->returnProdukService = $returnProdukService;
    }

    public function index(Request $request)
    {
        $search = $request->s ?? '';
        $distribusi = Distribusi::where('is_archived', false)->whereDoesntHave('retur_produk', function (Builder $query) {$query->where('is_archived', false); })->get();
        $distribusi = DistribusiResource::collection($distribusi)->resolve();
        $result = $this->returnProdukService->index($search);
        return Inertia::render('stafAdmin/ReturProduk', [
            'title' => 'Retur Produk',
            'retur_produk' => $result,
            'distribusi' => $distribusi,
            'search' => $search,
        ]);
    }

    public function create(ReturProdukRequest $request)
    {
        $result = $this->returnProdukService->create($request->validated());
        if($result) {
            return back()->with('toast', toast(Severity::Success, 'Create Retur Produk', 'Create Retur Produk Successfully'));
        }
        return back()->with('toast', toast(Severity::Error, 'Create Retur Produk', 'Create Retur Produk Failed'));
    }

    public function update(ReturProdukUpdateRequest $request, ReturProduk $retur)
    {
        $result = $this->returnProdukService->update($retur, $request->validated());
        if($result) {
            return redirect(route('stafAdmin.retur_produk'))->with('toast', toast(Severity::Success, 'Update Retur Produk', 'Update Retur Produk Successfully'));
        }
        return redirect(route('stafAdmin.retur_produk'))->with('toast', toast(Severity::Error, 'Update Retur Produk', 'Update Retur Produk Failed'));
    }

    public function destroy(ReturProduk $retur)
    {
        $result = $this->returnProdukService->destroy($retur);
        if($result) {
            return back()->with('toast', toast(Severity::Success, 'Delete Retur Produk', 'Delete Retur Produk Successfully'));
        }
        return back()->with('toast', toast(Severity::Error, 'Delete Retur Produk', 'Delete Retur Produk Failed'));
    }
}
