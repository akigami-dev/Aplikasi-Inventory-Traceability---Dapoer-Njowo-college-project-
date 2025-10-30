<?php

namespace App\Observers\StafProduksi;

use App\Models\LotBahanBaku;
use App\Models\LotPenggunaanLogs;
use App\Models\Produksi;

class LotPenggunaanLogsObserver
{
    public function increaseJumlahLot(?LotPenggunaanLogs $lotPenggunaanLogs = null, $jumlah = null, $lotBahanBakuID = null)
    {
        if(!$lotPenggunaanLogs && !$jumlah) return;

        if($jumlah && $lotBahanBakuID && !$lotPenggunaanLogs){
            $lotBahanBaku = LotBahanBaku::where('id', $lotBahanBakuID)->first();
            $lotBahanBaku->jumlah += $jumlah;
            $lotBahanBaku->save();
        }else{
            $lotPenggunaanLogs->lot_bahan_baku->jumlah = $lotPenggunaanLogs->lot_bahan_baku->jumlah + $lotPenggunaanLogs->jumlah;
            $lotPenggunaanLogs->lot_bahan_baku->save();
        }
    }

    public function decreaseJumlahLot(LotPenggunaanLogs $lotPenggunaanLogs)
    {
        if(!$lotPenggunaanLogs) return;

        $lotPenggunaanLogs->lot_bahan_baku->jumlah = $lotPenggunaanLogs->lot_bahan_baku->jumlah - $lotPenggunaanLogs->jumlah;
        $lotPenggunaanLogs->lot_bahan_baku->save();
    }

    public function created(LotPenggunaanLogs $lotPenggunaanLogs): void
    {
        $this->decreaseJumlahLot($lotPenggunaanLogs);
    }

    public function creating(LotPenggunaanLogs $lotPenggunaanLogs): void
    {
        $lotPenggunaanLogs->updated_at = now();
        $lotPenggunaanLogs->created_at = now();
    }

    public function updating(LotPenggunaanLogs $lotPenggunaanLogs): void
    {
        $this->increaseJumlahLot(jumlah: $lotPenggunaanLogs->getOriginal('jumlah'), lotBahanBakuID: $lotPenggunaanLogs->getOriginal('lot_bahan_baku_id'));
        $lotPenggunaanLogs->updated_at = now();
    }

    public function updated(LotPenggunaanLogs $lotPenggunaanLogs): void
    {
        $this->decreaseJumlahLot($lotPenggunaanLogs);
    }

    public function deleted(LotPenggunaanLogs $lotPenggunaanLogs): void
    {
        $this->increaseJumlahLot($lotPenggunaanLogs);
    }

    public function restored(LotPenggunaanLogs $lotPenggunaanLogs): void
    {
        $this->decreaseJumlahLot($lotPenggunaanLogs);
    }

    public function forceDeleted(LotPenggunaanLogs $lotPenggunaanLogs): void
    {
        $this->increaseJumlahLot($lotPenggunaanLogs);
    }
}
