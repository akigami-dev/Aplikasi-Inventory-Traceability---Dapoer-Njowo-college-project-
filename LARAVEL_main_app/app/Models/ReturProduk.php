<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturProduk extends Model
{
    protected $fillable = [
        'distribusi_id',
        'tanggal_retur',
        'jumlah_retur',
        'keterangan',
        'user_id',
        'status',
        'catatan_tambahan',
        'is_archived',
    ];

    public function distribusi():BelongsTo
    {
        return $this->belongsTo(Distribusi::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
