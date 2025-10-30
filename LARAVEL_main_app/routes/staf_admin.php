<?php

use App\Http\Controllers\StafAdmin\DistribusiController;
use App\Http\Controllers\StafAdmin\LotBahanBakuController;
use App\Http\Controllers\StafAdmin\MasterBahanBakuController;
use App\Http\Controllers\StafAdmin\MasterProdukController;
use App\Http\Controllers\StafAdmin\MasterSertifikasiController;
use App\Http\Controllers\StafAdmin\MasterSupplierController;
use App\Http\Controllers\StafAdmin\RecallProdukController;
use App\Http\Controllers\StafAdmin\RestokBahanBakuController;
use App\Http\Controllers\StafAdmin\ReturProdukController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:staf admin'])->group(function () {
    // Restok Bahan Baku
    Route::get("restok_bahan_baku", [RestokBahanBakuController::class, "index"])->name("stafAdmin.restok_bahan_baku");
    Route::post("restok_bahan_baku/create", [RestokBahanBakuController::class, "create"])->name("stafAdmin.restok_bahan_baku.create.restok");
    Route::put("restok_bahan_baku/{restokBahanBaku}/update", [RestokBahanBakuController::class, "update"])->name("stafAdmin.restok_bahan_baku.update.restok");
    Route::delete("restok_bahan_baku/{restokBahanBaku}/delete", [RestokBahanBakuController::class, "destroy"])->name("stafAdmin.restok_bahan_baku.delete.restok");

    // LOT Bahan Baku
    Route::get("lot_bahan_baku", [LotBahanBakuController::class, "index"])->name("stafAdmin.lot_bahan_baku");
    // Route::post("lot_bahan_baku/create", [LotBahanBakuController::class, "create"])->name("stafAdmin.lot_bahan_baku.create.restok");
    // Route::put("lot_bahan_baku/{restokBahanBaku}/update", [LotBahanBakuController::class, "update"])->name("stafAdmin.lot_bahan_baku.update.restok");
    // Route::delete("lot_bahan_baku/{restokBahanBaku}/delete", [LotBahanBakuController::class, "destroy"])->name("stafAdmin.lot_bahan_baku.delete.restok");

    // Distribusi Produk
    Route::get("distribusi", [DistribusiController::class, "index"])->name("stafAdmin.distribusi");
    Route::post("distribusi/create", [DistribusiController::class, "create"])->name("stafAdmin.distribusi.create.distribusi");
    Route::put("distribusi/{distribusi}/update", [DistribusiController::class, "update"])->name("stafAdmin.distribusi.update.distribusi");
    Route::delete("distribusi/{distribusi}/delete", [DistribusiController::class, "destroy"])->name("stafAdmin.distribusi.delete.distribusi");
    
    // retur Produk
    Route::get("retur_produk", [ReturProdukController::class, "index"])->name("stafAdmin.retur_produk");
    Route::post("retur_produk/create", [ReturProdukController::class, "create"])->name("stafAdmin.retur_produk.create.retur");
    Route::put("retur_produk/{retur}/update", [ReturProdukController::class, "update"])->name("stafAdmin.retur_produk.update.retur");
    Route::delete("retur_produk/{retur}/delete", [ReturProdukController::class, "destroy"])->name("stafAdmin.retur_produk.delete.retur");

    // Recall Produk
    Route::get("recall_produk", [RecallProdukController::class, "index"])->name("stafAdmin.recall_produk");
    Route::post("recall_produk/create", [RecallProdukController::class, "create"])->name("stafAdmin.recall_produk.create.recall");
    Route::put("recall_produk/{recall}/update", [RecallProdukController::class, "update"])->name("stafAdmin.recall_produk.update.recall");
    Route::delete("recall_produk/{recall}/delete", [RecallProdukController::class, "destroy"])->name("stafAdmin.recall_produk.delete.recall");
});