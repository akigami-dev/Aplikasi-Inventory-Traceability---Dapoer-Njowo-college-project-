<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\Owner\BiodataUMKMController;
use App\Http\Controllers\Owner\KelolaUserController;
use App\Http\Controllers\Owner\LokasiPenyimpananController;
use App\Http\Controllers\Owner\MasterKategoriController;
use App\Http\Controllers\StafAdmin\MasterBahanBakuController;
use App\Http\Controllers\StafAdmin\MasterProdukController;
use App\Http\Controllers\StafAdmin\MasterSertifikasiController;
use App\Http\Controllers\StafAdmin\MasterSupplierController;
use App\Http\Controllers\TraceController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:owner'])->group(function () {
    // KELOLA USER
    Route::post('register', [RegisteredUserController::class, 'store'])->name('register');
    Route::post('user/{user}/update', [RegisteredUserController::class, 'update'])->name('user.update');
    Route::delete('user/{user}/delete', [RegisteredUserController::class, 'destroy'])->name('user.delete');

    Route::get('kelola_user', [KelolaUserController::class, 'index'])->name('owner.kelola_user');
    Route::put('kelola_user/{user}/role', [KelolaUserController::class, 'update_role'])->name('owner.kelola_user.role.update');
    Route::post('kelola_user/create', [KelolaUserController::class, 'create_user'])->name('owner.kelola_user.user.create');
    Route::put('kelola_user/{user}/update', [KelolaUserController::class, 'update_user'])->name('owner.kelola_user.user.update');
    Route::delete('kelola_user/{user}/delete', [KelolaUserController::class, 'delete_user'])->name('owner.kelola_user.user.delete');
    // Route::put('kelola_user/{search}', [KelolaUserController::class, 'search'])->name('owner.kelola_user.search');

    Route::get('laporan', [LaporanController::class, 'index'])->name('owner.laporan');
    Route::post('laporan/export_pdf', [LaporanController::class, 'export_pdf'])->name('owner.laporan.export_pdf');
    // Route::get('laporan/produksi', [LaporanController::class, 'laporanProduksi'])->name('owner.laporan_produksi');
    // Route::get('laporan/restok_bahan_baku', [LaporanController::class, 'laporanRestokBahanBaku'])->name('owner.laporan_restok_bahan_baku');
    // Route::get('laporan/lot_bahan_baku', [LaporanController::class, 'laporanLotBahanBaku'])->name('owner.laporan_lot_bahan_baku');

    // Master Produk
    Route::get('master_produk', [MasterProdukController::class, 'index'])->name('stafAdmin.master_produk');
    Route::post('master_produk/create', [MasterProdukController::class, 'create'])->name('stafAdmin.master_produk.create.data_produk');
    Route::put('master_produk/{MasterProduk}/update', [MasterProdukController::class, 'update'])->name('stafAdmin.master_produk.update.data_produk');
    Route::delete('master_produk/{MasterProduk}/delete', [MasterProdukController::class, 'destroy'])->name('stafAdmin.master_produk.delete.data_produk');

    // Master Sertifikasi
    Route::get("master_sertifikasi", [MasterSertifikasiController::class, "index"])->name("stafAdmin.master_sertifikasi");
    Route::post("master_sertifikasi/create", [MasterSertifikasiController::class, "create"])->name("stafAdmin.master_sertifikasi.create.data_sertifikasi");
    Route::put("master_sertifikasi/{sertifikasi}/update", [MasterSertifikasiController::class, "update"])->name("stafAdmin.master_sertifikasi.update.data_sertifikasi");
    Route::delete("master_sertifikasi/{sertifikasi}/delete", [MasterSertifikasiController::class, "destroy"])->name("stafAdmin.master_sertifikasi.delete.data_sertifikasi");

    // Master Supplier
    Route::get("master_supplier", [MasterSupplierController::class, "index"])->name("stafAdmin.master_supplier");
    Route::post("master_supplier/create", [MasterSupplierController::class, "create"])->name("stafAdmin.master_supplier.create.supplier");
    Route::put("master_supplier/{supplier}/update", [MasterSupplierController::class, "update"])->name("stafAdmin.master_supplier.update.supplier");
    Route::delete("master_supplier/{supplier}/delete", [MasterSupplierController::class, "destroy"])->name("stafAdmin.master_supplier.delete.supplier");

    // Master Bahan Baku
    Route::get("master_bahan_baku", [MasterBahanBakuController::class, "index"])->name("stafAdmin.master_bahan_baku");
    Route::post("master_bahan_baku/create", [MasterBahanBakuController::class, "create"])->name("stafAdmin.master_bahan_baku.create.bahan_baku");
    Route::put("master_bahan_baku/{bahan_baku}/update", [MasterBahanBakuController::class, "update"])->name("stafAdmin.master_bahan_baku.update.bahan_baku");
    Route::delete("master_bahan_baku/{bahan_baku}/delete", [MasterBahanBakuController::class, "destroy"])->name("stafAdmin.master_bahan_baku.delete.bahan_baku");

    // Lokasi Penyimpanan
    Route::get("lokasi_penyimpanan", [LokasiPenyimpananController::class, "index"])->name("owner.lokasi_penyimpanan");
    Route::post("lokasi_penyimpanan/create", [LokasiPenyimpananController::class, "create"])->name("owner.lokasi_penyimpanan.create.penyimpanan");
    Route::put("lokasi_penyimpanan/{penyimpanan}/update", [LokasiPenyimpananController::class, "update"])->name("owner.lokasi_penyimpanan.update.penyimpanan");
    Route::delete("lokasi_penyimpanan/{penyimpanan}/delete", [LokasiPenyimpananController::class, "destroy"])->name("owner.lokasi_penyimpanan.delete.penyimpanan");
    
    // Biodata UMKM
    Route::get("biodata_umkm", [BiodataUMKMController::class, "index"])->name("owner.biodata_umkm");
    Route::post("biodata_umkm/createorupdate", [BiodataUMKMController::class, "createOrUpdate"])->name("owner.biodata_umkm.createorupdate.umkm");
    
    // Master Kategori
    Route::get("master_kategori", [MasterKategoriController::class, "index"])->name("owner.master_kategori");
    Route::post("master_kategori/create", [MasterKategoriController::class, "create"])->name("owner.master_kategori.create.kategori");
    Route::put("master_kategori/{kategori}/update", [MasterKategoriController::class, "update"])->name("owner.master_kategori.update.kategori");
    Route::delete("master_kategori/{kategori}/delete", [MasterKategoriController::class, "destroy"])->name("owner.master_kategori.delete.kategori");
    
});