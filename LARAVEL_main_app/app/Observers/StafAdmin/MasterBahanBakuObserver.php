<?php

namespace App\Observers\StafAdmin;

use App\Models\MasterBahanBaku;

class MasterBahanBakuObserver
{
    /**
     * Handle the MasterBahanBaku "created" event.
     */
    public function created(MasterBahanBaku $masterBahanBaku): void
    {
        //
    }

    /**
     * Handle the MasterBahanBaku "updated" event.
     */
    public function updated(MasterBahanBaku $masterBahanBaku): void
    {
        //
    }

    /**
     * Handle the MasterBahanBaku "deleted" event.
     */
    public function deleted(MasterBahanBaku $masterBahanBaku): void
    {
        //
    }

    public function creating(MasterBahanBaku $masterBahanBaku): void
    {
        // Get the last created product code
        $lastProduct = MasterBahanBaku::latest('id')->first();
        
        if ($lastProduct) {
            $lastKode = (int)substr($lastProduct->kode_bahan, 2); // Get the numeric part
            $newKode = $lastKode + 1;
        } else {
            $newKode = 1;
        }

        $masterBahanBaku->kode_bahan = 'BS' . str_pad($newKode, 4, '0', STR_PAD_LEFT);
        $masterBahanBaku->updated_at = now();
        $masterBahanBaku->created_at = now();
    }

    public function updating(MasterBahanBaku $masterBahanBaku): void
    {
        $masterBahanBaku->updated_at = now();
    }

    /**
     * Handle the MasterBahanBaku "restored" event.
     */
    public function restored(MasterBahanBaku $masterBahanBaku): void
    {
        //
    }

    /**
     * Handle the MasterBahanBaku "force deleted" event.
     */
    public function forceDeleted(MasterBahanBaku $masterBahanBaku): void
    {
        //
    }
}
