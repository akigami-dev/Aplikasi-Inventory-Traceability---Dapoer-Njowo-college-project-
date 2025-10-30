<?php

namespace App\Providers;

use App\Models\Distribusi;
use App\Models\LotBahanBaku;
use App\Models\LotPenggunaanLogs;
use App\Models\MasterBahanBaku;
use App\Models\MasterKategori;
use App\Models\MasterProduk;
use App\Models\Produksi;
use App\Models\RecallProduk;
use App\Models\RestokBahanBaku;
use App\Models\SertifikasiProduk;
use App\Models\Supplier;
use App\Observers\MasterProdukObserver;
use App\Observers\Owner\MasterKategoriObserver;
use App\Observers\SertifikasiProdukObserver;
use App\Observers\StafAdmin\DistribusiObserver;
use App\Observers\StafAdmin\LotBahanBakuObserver;
use App\Observers\StafAdmin\MasterBahanBakuObserver;
use App\Observers\StafAdmin\RecallProdukObserver;
use App\Observers\StafAdmin\RestokBahanBakuObserver;
use App\Observers\StafAdmin\SupplierObserver;
use App\Observers\StafProduksi\LotPenggunaanLogsObserver;
use App\Observers\StafProduksi\ProduksiObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        MasterProduk::observe(MasterProdukObserver::class);
        SertifikasiProduk::observe(SertifikasiProdukObserver::class);
        Supplier::observe(SupplierObserver::class);
        MasterBahanBaku::observe(MasterBahanBakuObserver::class);
        RestokBahanBaku::observe(RestokBahanBakuObserver::class);
        LotBahanBaku::observe(LotBahanBakuObserver::class);
        Produksi::observe(ProduksiObserver::class);
        LotPenggunaanLogs::observe(LotPenggunaanLogsObserver::class);
        Distribusi::observe(DistribusiObserver::class);
        RecallProduk::observe(RecallProdukObserver::class);
        MasterKategori::observe(MasterKategoriObserver::class);
    }
}
