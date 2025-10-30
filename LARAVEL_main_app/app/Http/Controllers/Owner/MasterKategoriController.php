<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\CreateKategoriRequest;
use App\Http\Requests\Owner\UpdateKategoriRequest;
use App\Models\MasterKategori;
use App\Services\Owner\KategoriService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Severity;

class MasterKategoriController extends Controller
{
    protected $kategoriService;

    public function __construct(KategoriService $kategoriService)
    {
        $this->kategoriService = $kategoriService;
    }


    public function index(Request $request)
    {
        $search = $request->s ?? '';
        $filter = $request->filter ?? [];
        $paginate = $request->paginate ?? 10;
        $result = $this->kategoriService->index(search: $search, paginate: $paginate, filter: $filter);
        return Inertia::render('owner/MasterKategori', [
            'title' => 'Master Kategori',
            'data' => $result,
            'search' => $search,
            'filter' => $filter,
            'paginate' => (int) $paginate
        ]);
    }

    public function create(CreateKategoriRequest $request)
    {
        $result = $this->kategoriService->create($request->validated());
        if($result) {
            return redirect()->route('owner.master_kategori')->with('toast', toast(Severity::Success, 'Create Kategori', 'Create Kategori Successfully'));
        }
        return redirect()->route('owner.master_kategori')->with('toast', toast(Severity::Error, 'Create Kategori', 'Create Kategori Failed'));
    }

    public function update(UpdateKategoriRequest $request, MasterKategori $kategori)
    {
        $result = $this->kategoriService->update($request->validated(), $kategori);
        if($result) {
            return redirect()->route('owner.master_kategori')->with('toast', toast(Severity::Success, 'Update Kategori', 'Update Kategori Successfully'));
        }
        return redirect()->route('owner.master_kategori')->with('toast', toast(Severity::Error, 'Update Kategori', 'Update Kategori Failed'));
    }

    public function destroy(MasterKategori $kategori)
    {
        $result = $this->kategoriService->delete($kategori);
        if($result) {
            return redirect()->route('owner.master_kategori')->with('toast', toast(Severity::Success, 'Delete Kategori', 'Delete Kategori Successfully'));
        }
        return redirect()->route('owner.master_kategori')->with('toast', toast(Severity::Error, 'Delete Kategori', 'Delete Kategori Failed'));
    }
}
