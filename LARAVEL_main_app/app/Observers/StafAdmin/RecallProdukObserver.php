<?php

namespace App\Observers\StafAdmin;

use App\Models\Distribusi;
use App\Models\RecallProduk;
use Illuminate\Support\Facades\Auth;

class RecallProdukObserver
{
    public function reduceJumlahTersisa(RecallProduk $recallProduk)
    {
        $distribusi = Distribusi::where('id', $recallProduk->distribusi_id)->first();
        $distribusi->jumlah_tersisa = $distribusi->jumlah_tersisa - $recallProduk->jumlah_recall;
        $distribusi->save();
    }

    public function generatekode(RecallProduk $recallProduk)
    {
        #RP2506110430 + kode distribusi + id recall produk
        $lastId = RecallProduk::latest('id')->first();
        if ($lastId) {
            $lastKode = (int)substr($lastId->kode_recall, 12 + strlen((int)$recallProduk->distribusi_id));
            $newKode = $lastKode + 1;
        } else {
            $newKode = 1;
        }

        $date = now()->format('ymdHi');
        // $date = parseDate($distribusi->tanggal_pengiriman, 'ymdHi');
        $uniqueCode = "RP". $date . $recallProduk->distribusi_id . $newKode; 
        $recallProduk->kode_recall = $uniqueCode ;
        $recallProduk->updated_at = now();
    }
    
    public function creating(RecallProduk $recallProduk)
    {
        $this->generatekode($recallProduk);
        $recallProduk->user_id = Auth::id();
        $recallProduk->created_at = now();
        $recallProduk->updated_at = now();
    }

    public function updating(RecallProduk $recallProduk)
    {
        $recallProduk->updated_at = now();
    }

    public function updated(RecallProduk $recallProduk)
    {
        if($recallProduk->is_archived == true) return;
        $this->reduceJumlahTersisa($recallProduk);
    }

    public function created(RecallProduk $recallProduk)
    {
        $this->reduceJumlahTersisa($recallProduk);
    }
}
