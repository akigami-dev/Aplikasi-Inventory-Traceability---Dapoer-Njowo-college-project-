<?php

namespace App\Http\Controllers\StafAdmin;

use App\Http\Controllers\Controller;
use App\Services\StafAdmin\LotBahanBakuService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LotBahanBakuController extends Controller
{
    protected $lotBahanBakuService;
    public function __construct(LotBahanBakuService $lotBahanBakuService)
    {
        $this->lotBahanBakuService = $lotBahanBakuService;
    }
    public function index(Request $request){
        $search = $request->s ?? '';
        $filter = $request->filter ?? [];
        $paginate = $request->paginate ?? 10;
        $result = $this->lotBahanBakuService->index($search, $paginate, $filter);
        return Inertia::render('stafAdmin/LotBahanBaku', [
            'title' => 'Lot Bahan Baku',
            'lot_bahan_baku' => $result,
            'search' => $search,
            'filter' => $filter,
            'paginate' => $paginate
        ]);
    }
}
