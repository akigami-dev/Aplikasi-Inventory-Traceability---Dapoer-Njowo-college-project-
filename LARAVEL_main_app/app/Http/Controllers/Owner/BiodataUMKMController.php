<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\BiodataUmkm;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Severity;

class BiodataUMKMController extends Controller
{
    public function index()
    {
        $biodata = BiodataUmkm::first() ?? [];
        return Inertia::render('owner/BiodataUMKM', [
            'title' => "Biodata UMKM",
            'biodata' => $biodata
        ]);
    }

    public function createOrUpdate(Request $request)
    { 
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'alamat' => ['required', 'string', 'max:255'],
            'no_telpon' => ['required', 'string', 'max:255', 'min:14', 'max:17'],
            'email' => ['required', 'string', 'email', 'max:255'],
        ]);
        
        try {
            BiodataUmkm::updateOrInsert(
                ['id' => 1],
                [
                'nama' => $validated['nama'],
                'alamat' => $validated['alamat'],
                'no_telpon' => $validated['no_telpon'],
                'email' => $validated['email'],
                ],
            );

            return to_route('owner.biodata_umkm')->with('toast', toast(Severity::Success, 'Success', 'Success'));
        } catch (\Throwable $th) {
            // dd($th);
            return to_route('owner.biodata_umkm')->with('toast', toast(Severity::Error, 'Failed', 'Failed'));
        }
    }
}
