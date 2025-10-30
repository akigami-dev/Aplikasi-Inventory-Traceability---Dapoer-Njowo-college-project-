<?php

namespace App\Http\Controllers\StafAdmin;

use App\Http\Controllers\Controller;
use App\Http\Resources\KategoriResource;
use App\Http\Resources\MasterProdukResource;
use App\Http\Resources\SertifikasiResource;
use App\Models\MasterKategori;
use App\Models\MasterProduk;
use App\Models\ProdukKategori;
use App\Models\Produksi;
use App\Models\Sertifikasi;
use App\Models\SertifikasiProduk;
use Error;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Severity;

class MasterProdukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->s ?? '';
        $paginate = $request->paginate ?? 10;
        $filter = $request->filter ?? [];
        $data = MasterProduk::getAll($search, $paginate, $filter);

        // dd($data->first()->produkKategori->first()->masterKategori()->withTrashed()->first());

        $data = MasterProdukResource::collection($data);
        
        $sertifikasi = Sertifikasi::get();
        $sertifikasi = SertifikasiResource::collection($sertifikasi)->resolve();
        
        // $kategori = MasterProduk::select('kategori')->where('is_archived', false)->distinct()->pluck('kategori')->map(fn($item) => titlecase($item));
        $kategori = MasterKategori::get();
        $kategori = KategoriResource::collection($kategori)->resolve();
        // dd();

        return Inertia::render('stafAdmin/MasterProduk', [
            'title' => 'Master Produk',
            'masterProduk' => $data,
            'sertifikasi' => $sertifikasi,
            'kategori' => $kategori,
            'search' => $search,
            'paginate' => (int) $paginate,
            'filter' => $filter
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'nama_produk' => 'required|string|'. Rule::unique('master_produks', 'nama_produk')->where('is_archived', false),
            'gambar' => 'image|max:2048',
            'template_label' => 'image|max:2048',
            'kategori' => ['required', 'integer', Rule::exists('master_kategoris', 'id')],
            'harga' => 'required|numeric|decimal:0,2',
            'berat_bersih' => 'required|numeric|decimal:0,2|min:1.00',
            'satuan_berat' => 'required|string',
            'sertifikasi' => 'nullable|array',
            'sertifikasi.*.sertifikasi.id' => 'required|integer|distinct:strict|'. Rule::unique('sertifikasi_produks', 'sertifikasi_id')->whereNull('deleted_at'),
            'sertifikasi.*.sertifikasi.kode_sertifikasi' => 'required|string',
        ]);
        // dd($validated);
        try {
            $imagePath = config('constants.storage_product_image_path');
            $gambar = $request->file('gambar');
            $label = $request->file('template_label');
            $labelContent = $label->getContent();
            $resultCheck = createLabel($labelContent, "templateLabel.png", "", "", "", "", "");

            if($resultCheck->failed()) {
                return back()->with('toast', toast(Severity::Error, 'Create Data Produk Failed!', 'Template Label Tidak Sesuai.'))->withErrors(['template_label' => 'Template label tidak sesuai.']);
            }

            if($request->hasFile('gambar') && $request->hasFile('template_label')) {
                $last_id = MasterProduk::latest('id')->first();
                $last_id = $last_id ? $last_id->id + 1 : 1;
                
                // Save image to public storage
                $imageName = time() . "_{$last_id}_products." . $gambar->getClientOriginalExtension();
                $labelName = time() . "_{$last_id}_templateLabel." . $label->getClientOriginalExtension();
                $gambar->storeAs($imagePath, $imageName, 'public');
                $label->storeAs($imagePath, $labelName, 'public');

                $MasterProduk= new MasterProduk();
                $MasterProduk->nama_produk = $request->nama_produk;
                $MasterProduk->gambar_path = $imageName;
                $MasterProduk->harga = $request->harga;
                $MasterProduk->berat_bersih = $request->berat_bersih;
                $MasterProduk->satuan_berat = $request->satuan_berat;
                $MasterProduk->template_label_path = $labelName;
                $MasterProduk->save();

                $produkKategori = new ProdukKategori();
                $produkKategori->master_produk_id = (int) $MasterProduk->id;
                $produkKategori->master_kategori_id = (int) $request->kategori;
                $produkKategori->save();

                if($request->sertifikasi){
                    foreach ($request->sertifikasi as $s) {
                        SertifikasiProduk::create([
                            'master_produk_id' => MasterProduk::latest('id')->first()->id,
                            'sertifikasi_id' => (int)$s['sertifikasi']['id'],
                        ]);
                    }
                }
            }

            return to_route('stafAdmin.master_produk')->with('toast', toast(Severity::Success, 'Create Data Produk Success!', 'Data produk berhasil ditambahkan.'));
        } catch (\Throwable $th) {
            dd($th);
            return to_route('stafAdmin.master_produk')->with('toast', toast(Severity::Error, 'Create Data Produk Failed!', 'Data produk gagal ditambahkan.'));
        }
    }
    
    public function update(MasterProduk $MasterProduk, Request $request)
    {
        $existingIds = $MasterProduk->sertifikasiProduk->pluck('sertifikasi_id')->all();
        $validated = $request->validate([
            'nama_produk' => 'required|string|'. Rule::unique('master_produks', 'nama_produk')->ignore($MasterProduk->id)->where('is_archived', false),
            'gambar' => 'nullable|image|max:2048',
            'template_label' => 'nullable|image|max:2048',
            'kategori' => ['required', 'integer', Rule::exists('master_kategoris', 'id')],
            'harga' => 'required|numeric|decimal:0,2',
            'berat_bersih' => 'required|numeric|decimal:0,2|min:1.00',
            'satuan_berat' => 'required|string',
            'sertifikasi' => 'nullable|array',
            'sertifikasi.*.sertifikasi.id' => [
                'required',
                'integer',
                'distinct:strict',
                function ($attribute, $value, $fail) use ($existingIds) {
                    if (!in_array($value, $existingIds)) {
                        $exists = SertifikasiProduk::where('sertifikasi_id', $value)->exists();
                        if ($exists) {
                            $fail("Sertifikasi sudah digunakan untuk produk lain.");
                        }
                    }
                },
            ],
            'sertifikasi.*.sertifikasi.kode_sertifikasi' => 'required|string',
        ]);
        try {
            $imagePath = config('constants.storage_product_image_path');

            if($request->hasFile('gambar')) {
                $gambar = $request->file('gambar');

                // Save image to public storage
                $imageName = time() . "_{$MasterProduk->id}_products." . $gambar->getClientOriginalExtension();
                $gambar->storeAs($imagePath, $imageName, 'public');
                deleteImage($imagePath, $MasterProduk->gambar_path);
                $MasterProduk->gambar_path = $imageName;
            }

            if($request->hasFile('template_label')) {
                $label = $request->file('template_label');
                
                $labelContent = $label->getContent();
                $resultCheck = createLabel($labelContent, "templateLabel.png", "", "", "", "", "");

                if($resultCheck->failed()) {
                    return back()->with('toast', toast(Severity::Error, 'Create Data Produk Failed!', 'Template Label Tidak Sesuai.'))->withErrors(['template_label' => 'Template label tidak sesuai.']);
                }

                $labelName = time() . "_{$MasterProduk->id}_templateLabel." . $label->getClientOriginalExtension();
                $label->storeAs($imagePath, $labelName, 'public');
                deleteImage($imagePath, $MasterProduk->template_label_path);
                $MasterProduk->template_label_path = $labelName;
            }

            SertifikasiProduk::where('master_produk_id', '=', (int)$MasterProduk->id)->delete();
            if($request->sertifikasi){
                $savedSertifikasi = [];
                foreach($request->sertifikasi as $s){
                    $savedSertifikasi[] = [
                        'master_produk_id' => (int)$MasterProduk->id,
                        'sertifikasi_id' => (int)$s['sertifikasi']['id'],
                    ];
                };
                SertifikasiProduk::insert($savedSertifikasi);
            }
            
            $kategoriID = $MasterProduk->produkKategori->first()->master_kategori_id;
            if($kategoriID != $request->kategori){
                // $isExist = MasterKategori::where('id', '=', $kategoriID)->whereNull('deleted_at')->exists();
                // dd($isExist);
                // if($isExist){
                //     // dd($MasterProduk->produkKategori->first());
                //     $MasterProduk->produkKategori->first()->delete();
                // }
                $MasterProduk->produkKategori->first()->delete();
                ProdukKategori::create([
                    'master_produk_id' => $MasterProduk->id,
                    'master_kategori_id' => $request->kategori,
                ]);
            }

            $MasterProduk->nama_produk = $request->nama_produk;
            $MasterProduk->harga = $request->harga;
            $MasterProduk->berat_bersih = $request->berat_bersih;
            $MasterProduk->satuan_berat = $request->satuan_berat;
            $MasterProduk->save();

            return to_route('stafAdmin.master_produk')->with('toast', toast(Severity::Success, 'Update Data Produk Success!', 'Data produk berhasil diubah.'));
        } catch (\Throwable $th) {
            dd($th);
            return to_route('stafAdmin.master_produk')->with('toast', toast(Severity::Error, 'Update Data Produk Failed!', 'Data produk gagal diubah.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MasterProduk $MasterProduk)
    {
        try {
            $MasterProduk->is_archived= true;
            $MasterProduk->save();
            $MasterProduk->sertifikasiProduk->each->delete();
            $MasterProduk->produkKategori->each->delete();
            return to_route('stafAdmin.master_produk')->with('toast', toast(Severity::Success, 'Delete Data Produk Success!', 'Data produk berhasil dihapus.'));
        } catch (\Throwable $th) {
            dd("im here CATCH", $th);
            return to_route('stafAdmin.master_produk')->with('toast', toast(Severity::Error, 'Delete Data Produk Failed!', 'Data produk gagal dihapus.'));
        }
    }
}
