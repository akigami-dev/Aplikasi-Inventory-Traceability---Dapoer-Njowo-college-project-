<?php

namespace App\Http\Controllers\StafAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StafAdmin\RecallProdukRequest;
use App\Models\RecallProduk;
use App\Services\StafAdmin\DistribusiService;
use App\Services\StafAdmin\RecallProdukService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Severity;

class RecallProdukController extends Controller
{
    protected $recallProdukService;
    protected $distribusiService;

    public function __construct(RecallProdukService $recallProdukService, DistribusiService $distribusiService)
    {
        $this->recallProdukService = $recallProdukService;       
        $this->distribusiService = $distribusiService;
    }
    
    public function index(Request $request)
    {
        $search = $request->s ?? '';
        $filter = $request->filter ?? [];
        $paginate = $request->paginate ?? 10;
        $result = $this->recallProdukService->index($search, $paginate, $filter);
        $distribusi = $this->distribusiService->getDistribusi();

        $alasan_recall = $this->recallProdukService->getAlasanRecall();
        
        return Inertia::render('stafAdmin/RecallProduk', [
            'title' => 'Recall Produk',
            'recall_produk' => $result,
            'distribusi' => $distribusi,
            'alasan_recall' => $alasan_recall,
            'search' => $search,
            'user_id' => Auth::id(),
            'filter' => $filter,
            'paginate' => (int) $paginate
        ]);
    }

    public function create(RecallProdukRequest $request)
    {
        $result = $this->recallProdukService->create($request->validated());
        if($result) {
            return back()->with('toast', toast(Severity::Success, 'Create Recall Produk', 'Create Recall Produk Successfully'));
        }
        return back()->withErrors(['message' => 'Create Recall Produk Failed'])->with('toast', toast(Severity::Error, 'Create Recall Produk', 'Create Recall Produk Failed'));
    }

    public function update(RecallProduk $recall, RecallProdukRequest $request)
    {
        $result = $this->recallProdukService->update($request->validated(), $recall);
        if($result) {
            return back()->with('toast', toast(Severity::Success, 'Update Recall Produk', 'Update Recall Produk Successfully'));
        }
        return back()->withErrors(['message' => 'Update Recall Produk Failed'])->with('toast', toast(Severity::Error, 'Update Recall Produk', 'Update Recall Produk Failed'));
    }

    public function destroy(RecallProduk $recall)
    {
        $result = $this->recallProdukService->delete($recall);
        if($result) {
            return back()->with('toast', toast(Severity::Success, 'Delete Recall Produk', 'Delete Recall Produk Successfully'));
        }
        return back()->withErrors(['message' => 'Delete Recall Produk Failed'])->with('toast', toast(Severity::Error, 'Delete Recall Produk', 'Delete Recall Produk Failed'));
    }
}
