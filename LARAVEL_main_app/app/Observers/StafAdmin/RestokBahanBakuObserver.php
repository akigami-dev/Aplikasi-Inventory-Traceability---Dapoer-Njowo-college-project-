<?php

namespace App\Observers\StafAdmin;

use App\Models\RestokBahanBaku;

class RestokBahanBakuObserver
{
    /**
     * Handle the RestokBahanBaku "created" event.
     */
    public function created(RestokBahanBaku $restokBahanBaku): void
    {
        //
    }

    /**
     * Handle the RestokBahanBaku "updated" event.
     */
    public function updated(RestokBahanBaku $restokBahanBaku): void
    {
        //
    }

    public function creating(RestokBahanBaku $restokBahanBaku): void
    {
        $dateNow = now()->format('ymdHi');
        
        $lastId = RestokBahanBaku::latest('id')->first();
        
        if ($lastId) {
            $lastKode = (int)substr($lastId->kode_restok, 12 + strlen((int)$lastId->bahan_baku_id));
            $newKode = $lastKode + 1;
        } else {
            $newKode = 1;
        }

        // example: RK100525021397
        $uniqueCode = "RK". $dateNow . $restokBahanBaku->master_bahan_baku_id . $newKode; 
        $restokBahanBaku->kode_restok = $uniqueCode ;
        $restokBahanBaku->updated_at = now();
        $restokBahanBaku->created_at = now();
    }

    public function updating(RestokBahanBaku $restokBahanBaku): void
    {
        $restokBahanBaku->updated_at = now();
    }

    /**
     * Handle the RestokBahanBaku "deleted" event.
     */
    public function deleted(RestokBahanBaku $restokBahanBaku): void
    {
        //
    }

    /**
     * Handle the RestokBahanBaku "restored" event.
     */
    public function restored(RestokBahanBaku $restokBahanBaku): void
    {
        //
    }

    /**
     * Handle the RestokBahanBaku "force deleted" event.
     */
    public function forceDeleted(RestokBahanBaku $restokBahanBaku): void
    {
        //
    }
}
