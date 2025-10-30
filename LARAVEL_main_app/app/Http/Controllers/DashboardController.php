<?php

namespace App\Http\Controllers;

use App\Models\Distribusi;
use App\Models\Produksi;
use App\Models\RecallProduk;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $months = collect();
        $now = Carbon::now()->startOfMonth(); // start of current month
        $startMonth = $now->copy()->subMonths(2); // go back 6 months

        for ($date = $startMonth; $date <= $now; $date->addMonth()) {
            $start = $date->copy()->startOfMonth();
            $end = $date->copy()->endOfMonth();

            $monthKey = $date->format('Y-m'); // e.g., "2025-01"

            $jumlahProduksi = Produksi::whereBetween('created_at', [$start, $end])
                ->sum('kuantitas');

            $sisaDistribusi = Distribusi::whereBetween('created_at', [$start, $end])
                ->sum('jumlah_tersisa'); // or use condition like ->where('status', 'belum') etc.

            $jumlahRecall = RecallProduk::whereBetween('created_at', [$start, $end])
                ->sum('jumlah_recall');

            $months[$monthKey] = [
                'jumlah_produksi' => (int) $jumlahProduksi,
                'sisa_jumlah_distribusi' => (int) $sisaDistribusi,
                'jumlah_recall' => (int) $jumlahRecall,
            ];
        }
        $labels = [];
        $produksi = [];
        $distribusi = [];
        $recall = [];

        foreach ($months as $key => $value) {
            $labels[] = Carbon::parse($key . '-01')->translatedFormat('F');
            $produksi[] = $value['jumlah_produksi'];
            $distribusi[] = $value['sisa_jumlah_distribusi'];
            $recall[] = $value['jumlah_recall'];
        }
        // dd([$labels, $produksi, $distribusi, $recall]);
        // $users = User::with('role')->get();
        $testing = Auth::user();
        return Inertia::render('Dashboard', [
            'title' => "Dashboard page",
            'data' => [
                'labels' => $labels,
                'produksi' => $produksi,
                'distribusi' => $distribusi,
                'recall' => $recall,
            ],
            // 'users' => $users,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
