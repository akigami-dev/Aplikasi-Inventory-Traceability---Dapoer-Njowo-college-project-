<?php

namespace App\Observers;

use App\Models\Sertifikasi;

class SertifikasiObserver
{
    /**
     * Handle the Sertifikasi "created" event.
     */
    public function created(Sertifikasi $sertifikasi): void
    {
        //
    }

    /**
     * Handle the Sertifikasi "updated" event.
     */
    public function updated(Sertifikasi $sertifikasi): void
    {
        //
    }

    public function creating(Sertifikasi $sertifikasi): void
    {
        $sertifikasi->updated_at = now();
        $sertifikasi->created_at = now();
    }

    public function updating(Sertifikasi $sertifikasi): void
    {
        $sertifikasi->updated_at = now();
    }

    /**
     * Handle the Sertifikasi "deleted" event.
     */
    public function deleted(Sertifikasi $sertifikasi): void
    {
        //
    }

    /**
     * Handle the Sertifikasi "restored" event.
     */
    public function restored(Sertifikasi $sertifikasi): void
    {
        //
    }

    /**
     * Handle the Sertifikasi "force deleted" event.
     */
    public function forceDeleted(Sertifikasi $sertifikasi): void
    {
        //
    }
}
