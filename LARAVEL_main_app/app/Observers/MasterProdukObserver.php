<?php

namespace App\Observers;

use App\Models\MasterProduk;

class MasterProdukObserver
{
    /**
     * Handle the MasterProduk "created" event.
     */
    public function creating(MasterProduk $masterProduk): void
    {
        // Get the last created product code
        $lastProduct = MasterProduk::latest('id')->first();
        
        if ($lastProduct) {
            $lastKode = (int)substr($lastProduct->kode_produk, 2); // Get the numeric part
            $newKode = $lastKode + 1;
        } else {
            $newKode = 1;
        }
        
        // Format the code with leading zeros (PK001, PK002, etc.)
        $masterProduk->kode_produk = 'PK' . str_pad($newKode, 3, '0', STR_PAD_LEFT);
        $masterProduk->created_at = now();
        $masterProduk->updated_at = now();
    }

    /**
     * Handle the MasterProduk "updated" event.
     */
    public function updated(MasterProduk $masterProduk): void
    {
        //
    }

    /**
     * Handle the MasterProduk "deleted" event.
     */
    public function deleted(MasterProduk $masterProduk): void
    {
        //
    }

    /**
     * Handle the MasterProduk "restored" event.
     */
    public function restored(MasterProduk $masterProduk): void
    {
        //
    }

    /**
     * Handle the MasterProduk "force deleted" event.
     */
    public function forceDeleted(MasterProduk $masterProduk): void
    {
        //
    }
}
