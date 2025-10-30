<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProdukKategori extends Model
{
    use SoftDeletes;
    protected $table = 'produk_kategoris';

    protected $fillable = [
        'master_produk_id',
        'master_kategori_id',
    ];

    public function masterProduk() : BelongsTo
    {
        return $this->belongsTo(MasterProduk::class);
    }

    public function masterKategori() : BelongsTo
    {
        return $this->belongsTo(MasterKategori::class);
    }
}
