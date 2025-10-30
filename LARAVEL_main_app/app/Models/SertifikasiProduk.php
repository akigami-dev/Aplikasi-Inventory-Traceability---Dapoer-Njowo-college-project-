<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SertifikasiProduk extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'master_produk_id',
        'sertifikasi_id',
        'kode_sertifikasi',
    ];

    public function sertifikasi(): BelongsTo
    {
        return $this->BelongsTo(Sertifikasi::class);
    }
    public function masterProduk(): BelongsTo
    {
        return $this->BelongsTo(MasterProduk::class);
    }
}
