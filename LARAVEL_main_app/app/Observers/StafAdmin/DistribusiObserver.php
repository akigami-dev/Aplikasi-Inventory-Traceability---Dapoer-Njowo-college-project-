<?php

namespace App\Observers\StafAdmin;

use App\Models\Distribusi;

class DistribusiObserver
{
    public function generateKode(Distribusi $distribusi){
        $lastId = Distribusi::latest('id')->first();

        if ($lastId) {
            $lastKode = (int)substr($lastId->kode_distribusi, 14 + strlen((int)$lastId->produksi_id));
            $newKode = $lastKode + 1;
        } else {
            $newKode = 1;
        }

        // example: DIST250609185011
        $date = now()->format('ymdHi');
        // $date = parseDate($distribusi->tanggal_pengiriman, 'ymdHi');
        $uniqueCode = "DIST". $date . $distribusi->produksi_id . $newKode; 
        $distribusi->kode_distribusi = $uniqueCode ;
        $distribusi->updated_at = now();
    }

    public function creating(Distribusi $distribusi): void
    {
        $this->generateKode($distribusi);
        $distribusi->created_at = now();
    }

    public function updating(Distribusi $distribusi): void
    {
        // $distribusi->updated_at = now();
        // $this->generateKode($distribusi);
    }
}
