<?php

namespace App\Observers\StafAdmin;

use App\Models\LotBahanBaku;

class LotBahanBakuObserver
{
    /**
     * Handle the LotBahanBaku "created" event.
     */
    public function created(LotBahanBaku $lotBahanBaku): void
    {
        //
    }

    /**
     * Handle the LotBahanBaku "updated" event.
     */
    public function updated(LotBahanBaku $lotBahanBaku): void
    {
        //
    }

    public function creating(LotBahanBaku $lotBahanBaku): void
    {
        $dateNow = now()->format('ymdHi');
        
        $lastId = LotBahanBaku::latest('id')->first();
        
        if ($lastId) {
            $lastKode = (int)substr($lastId->kode_lot, 15 + strlen((int)$lastId->kodeRestok));
            $newKode = $lastKode + 1;
        } else {
            $newKode = 1;
        }

        // example: RK100525021397
        $uniqueCode = "LOTBK". $dateNow . $lotBahanBaku->restok_bahan_baku_id . $newKode; 
        $lotBahanBaku->kode_lot = $uniqueCode ;
        $lotBahanBaku->updated_at = now();
        $lotBahanBaku->created_at = now();
    }

    public function updating(LotBahanBaku $lotBahanBaku): void
    {
        $lotBahanBaku->updated_at = now();
    }

    /**
     * Handle the LotBahanBaku "deleted" event.
     */
    public function deleted(LotBahanBaku $lotBahanBaku): void
    {
        //
    }

    /**
     * Handle the LotBahanBaku "restored" event.
     */
    public function restored(LotBahanBaku $lotBahanBaku): void
    {
        //
    }

    /**
     * Handle the LotBahanBaku "force deleted" event.
     */
    public function forceDeleted(LotBahanBaku $lotBahanBaku): void
    {
        //
    }

    public function saving(LotBahanBaku $lot)
    {
        $now = now();

        if ($lot->jumlah <= 0) {
            $lot->status = 'habis';
        } elseif ($lot->tanggal_kadaluarsa <= $now) {
            $lot->status = 'expired';
        } else {
            $lot->status = 'tersedia';
        }
    }
}
