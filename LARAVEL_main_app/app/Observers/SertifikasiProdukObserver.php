<?php

namespace App\Observers;

use App\Models\SertifikasiProduk;

class SertifikasiProdukObserver
{
    /**
     * Handle the SertifikasiProduk "created" event.
     */
    public function created(SertifikasiProduk $SertifikasiProduk): void
    {
        //
    }

    /**
     * Handle the SertifikasiProduk "updated" event.
     */
    public function updated(SertifikasiProduk $SertifikasiProduk): void
    {
        //
    }

    public function creating(SertifikasiProduk $SertifikasiProduk): void
    {
        $SertifikasiProduk->updated_at = now();
        $SertifikasiProduk->created_at = now();
    }

    public function updating(SertifikasiProduk $SertifikasiProduk): void
    {
        $SertifikasiProduk->updated_at = now();
    }

    /**
     * Handle the SertifikasiProduk "deleted" event.
     */
    public function deleted(SertifikasiProduk $SertifikasiProduk): void
    {
        //
    }

    /**
     * Handle the SertifikasiProduk "restored" event.
     */
    public function restored(SertifikasiProduk $SertifikasiProduk): void
    {
        //
    }

    /**
     * Handle the SertifikasiProduk "force deleted" event.
     */
    public function forceDeleted(SertifikasiProduk $SertifikasiProduk): void
    {
        //
    }
}
