<?php

namespace App\Observers\StafProduksi;

use App\Models\Produksi;

class ProduksiObserver
{
    /**
     * Handle the Produksi "created" event.
     */
    public function created(Produksi $produksi): void
    {
        $dateNow = now()->format('ymdHi');

        $nomorBatch = 'PRD'. $dateNow . $produksi->master_produk_id . $produksi->id;
        $produksi->nomor_batch = $nomorBatch;
        $produksi->save();
    }

    /**
     * Handle the Produksi "updated" event.
     */
    public function updated(Produksi $produksi): void
    {
        if($produksi->is_archived == true){
            $produksi->lot_penggunaan_logs->each->delete();
        }
    }

    public function creating(Produksi $produksi): void
    {
        
        $produksi->updated_at = now();
        $produksi->created_at = now();
    }

    public function updating(Produksi $produksi): void
    {
        $produksi->updated_at = now();
    }

    /**
     * Handle the Produksi "deleted" event.
     */
    public function deleted(Produksi $produksi): void
    {
        //
    }

    /**
     * Handle the Produksi "restored" event.
     */
    public function restored(Produksi $produksi): void
    {
        //
    }

    /**
     * Handle the Produksi "force deleted" event.
     */
    public function forceDeleted(Produksi $produksi): void
    {
        //
    }
}
