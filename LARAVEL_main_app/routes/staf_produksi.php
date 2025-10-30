<?php

use App\Http\Controllers\StafProduksi\ProduksiController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:staf produksi'])->group(function () {
    // Restok Bahan Baku
    Route::get("produksi", [ProduksiController::class, "index"])->name("stafProduksi.produksi");
    Route::post("produksi/create", [ProduksiController::class, "create"])->name("stafProduksi.produksi.create.produksi");
    Route::put("produksi/{produksi}/update", [ProduksiController::class, "update"])->name("stafProduksi.produksi.update.produksi");
    Route::delete("produksi/{produksi}/delete", [ProduksiController::class, "destroy"])->name("stafProduksi.produksi.delete.produksi");
    Route::put("produksi/{produksi}/proses", [ProduksiController::class, "proses"])->name("stafProduksi.produksi.proses.produksi");
    Route::put("produksi/{produksi}/selesai", [ProduksiController::class, "selesai"])->name("stafProduksi.produksi.selesai.produksi");
    Route::put("produksi/{produksi}/edit_kadaluarsa", [ProduksiController::class, "edit_kadaluarsa"])->name("stafProduksi.produksi.edit_kadaluarsa.produksi");
});