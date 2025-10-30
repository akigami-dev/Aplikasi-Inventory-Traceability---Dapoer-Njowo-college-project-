<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LotBahanBaku extends Model
{
    // protected $table = 'lot_bahan_bakus';
    protected $fillable = [
        'kode_lot',
        'restok_bahan_baku_id',
        'status',
        'tanggal_kadaluarsa',
        'jumlah',
        'satuan',
        'is_archived',
    ];
    
    public static function getField(String $field)
    {
        switch ($field) {
            case 'created at':
                return 'created_at';
                break;
            case 'kode lot':
                return 'kode_lot';
                break;
            case 'jumlah':
                return 'jumlah';
                break;
            case 'jumlah tersisa':
                return 'jumlah';
                break;
            case 'satuan':
                return 'satuan';
                break;
            case 'tanggal kadaluarsa':
                return 'tanggal_kadaluarsa';
                break;
            case 'status':
                return 'status';
                break;
            default:
                return false;
                break;
        }
    }

    public function restok_bahan_baku(): BelongsTo
    {
        return $this->BelongsTo(RestokBahanBaku::class, 'restok_bahan_baku_id');
    }

    protected static function booted()
    {
        // static::retrieved(function($lot_bahan_baku) {
        //     $currentDate = now();
            
        //     if ($lot_bahan_baku->jumlah <= 0) {
        //         $lot_bahan_baku->status = 'habis';
        //     } elseif ($lot_bahan_baku->tanggal_kadaluarsa <= $currentDate) {
        //         $lot_bahan_baku->status = 'expired';
        //     } else {
        //         $lot_bahan_baku->status = 'tersedia';
        //     }

        //     if ($lot_bahan_baku->isDirty('status')) {
        //         $lot_bahan_baku->save();
        //     }
        // });
    }
}
