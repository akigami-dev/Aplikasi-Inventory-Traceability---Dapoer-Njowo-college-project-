<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Distribusi extends Model
{
    protected $fillable = [
        'kode_distribusi',
        'produksi_id',
        'nama_retailer',
        'alamat',
        'tanggal_pengiriman',
        'jumlah_tersisa',
        'is_archived',
    ];

    public static function getField(String $field)
    {
        switch ($field) {
            case 'created at':
                return 'created_at';
                break;
            case 'kode distribusi':
                return 'kode_distribusi';
                break;
            case 'nama retailer':
                return 'nama_retailer';
                break;
            case 'alamat':
                return 'alamat';
                break;
            case 'tanggal pengiriman':
                return 'tanggal_pengiriman';
                break;
            case 'jumlah tersisa':
                return 'jumlah_tersisa';
                break;
            
            default:
                return false;
                break;
        }
    }

    public function produksi() : BelongsTo
    {
        return $this->belongsTo(Produksi::class);
    }

    public function retur_produk(): HasMany
    {
        return $this->hasMany(ReturProduk::class);
    }

    public function recall_produk(): HasMany
    {
        return $this->hasMany(RecallProduk::class);
    }
}
