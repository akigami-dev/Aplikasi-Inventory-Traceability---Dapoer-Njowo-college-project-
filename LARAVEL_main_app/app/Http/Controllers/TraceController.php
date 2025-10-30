<?php

namespace App\Http\Controllers;

use App\Exports\ArrayExport;
use App\Exports\UsersExport;
use App\Http\Resources\MasterBahanBakuResource;
use App\Models\MasterBahanBaku;
use App\Models\Produksi;
use App\Models\User;
use App\Services\TraceService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use Severity;

use function PHPUnit\Framework\isArray;

class TraceController extends Controller
{
    protected $traceService;
    public function __construct(TraceService $traceService)
    {
        $this->traceService = $traceService;
    }

    // only GUEST
    public function index()
    {
        $auth = Auth::user();
        $authRole = $auth->role->nama_role ?? null;
        if($auth && ($authRole == 'staf admin' || $authRole == 'owner')) return redirect(route('auth.trace', ['s' => $trace]));

        // $result = $this->traceService->index($trace);
        // if(!$result) abort(404);

        return Inertia::render('Trace', [
            'title' => "Trace page",
            // 'trace' => $result,
            // 'trace' => [],
        ]);
    }

    public function guestIndex(String $trace){
        $result = $this->traceService->index($trace);
        
        if(!$result) return redirect()->route('trace')->with('toast', toast(Severity::Error, 'Search Failed', 'Data Tidak Dapat Ditemukan'));

        return Inertia::render('Trace', [
            'title' => "Trace page | ". $trace,
            'trace' => $result,
        ]);
    }

    // only OWNER and STAF ADMIN
    public function authIndex(Request $request){
        $search = $request->s ?? '';
        $filter = $request->filter ?? [];
        $paginate = $request->paginate ?? 10;
        $styleView = $request->styleView ?? 'List';
        // dd($filter);
        $result = $this->traceService->authIndex($search, $filter, $paginate);
        // if(!$result && $search == '') abort(404);
        return Inertia::render('TraceAuth', [
            'title' => "Trace page",
            'trace' => $result,
            'search' => $search ?? '',
            'filter' => $filter,
            'styleView' => $styleView
        ]);
    }

    public function authTrace(String $trace){
        return Inertia::render('TraceAuth', [
            'title' => "Trace page",
        ]);
    }

    public function pdf_product(Request $request){
        $trace = $request->trace ?? abort(404);
        return Inertia::render('pdfs/TraceProduct',[
            'title' => "Trace page",
            'trace' => $trace
        ]);
    }
}
