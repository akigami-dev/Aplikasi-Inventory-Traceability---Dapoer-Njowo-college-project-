<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LotPenggunaanLogs extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'produksi_id',
        'lot_bahan_baku_id',
        'jumlah',
        'tanggal_penggunaan',
        'user_id',
    ];

    public function lot_bahan_baku(): BelongsTo
    {
        return $this->belongsTo(LotBahanBaku::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
