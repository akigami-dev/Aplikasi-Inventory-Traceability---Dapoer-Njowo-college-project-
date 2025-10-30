<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Produksi extends Model
{
    protected $fillable = [
        'nomor_batch',
        'master_produk_id',
        'kuantitas',
        'tanggal_kadaluarsa',
        'status',
        'lokasi_penyimpanan_id',
        'suhu_penyimpanan',
        'mulai_produksi',
        'selesai_produksi',
        'qrcode_path',
        'label_path',
        'user',
        'is_archived',
    ];

    public static function getField(String $field){
        switch ($field) {
            case 'created at':
                return 'created_at';
                break;
            case 'mulai produksi':
                return 'mulai_produksi';
                break;
            case 'selesai produksi':
                return 'selesai_produksi';
                break;
            case 'tanggal kadaluarsa':
                return 'tanggal_kadaluarsa';
                break;
            case 'status produksi':
                return 'status';
                break;
            case 'status':
                return 'status';
                break;
            case 'jumlah produksi':
                return 'kuantitas';
                break;
            case 'jumlah':
                return 'kuantitas';
                break;
            case 'kuantitas':
                return 'kuantitas';
                break;
            
            default:
                false;
                break;
        }
    }

    public function scopeSearch($query, $search = '')
    {
        if($search) {
            $query
            ->where('nomor_batch', 'like', '%'.$search.'%')
            ->orWhere('status', '=', $search)
            ->orWhere('kuantitas', '=', $search)
            ->orWhere(function (Builder $qdate) use ($search) {
                $qdate->whereYear('mulai_produksi', $search)
                    ->orWhereMonth('mulai_produksi', $search)
                    ->orWhereDay('mulai_produksi', $search)
                    ->orWhereDate('mulai_produksi', $search)
                    ->orWhereTime('mulai_produksi', '>', $search);
            })
            ->orWhere(function (Builder $qdate) use ($search) {
                $qdate->whereYear('selesai_produksi', $search)
                    ->orWhereMonth('selesai_produksi', $search)
                    ->orWhereDay('selesai_produksi', $search)
                    ->orWhereDate('selesai_produksi', $search)
                    ->orWhereTime('selesai_produksi', '>', $search);
            })
            ->orWhere(function (Builder $qdate) use ($search) {
                $qdate->whereYear('tanggal_kadaluarsa', $search)
                    ->orWhereMonth('tanggal_kadaluarsa', $search)
                    ->orWhereDay('tanggal_kadaluarsa', $search)
                    ->orWhereDate('tanggal_kadaluarsa', $search);
            })
            ->orWhereHas('master_produk', function (Builder $qmp) use($search) {
                $qmp
                ->where('nama_produk', 'like', '%'.$search.'%')
                ->orWhere('kode_produk', 'like', '%'.$search.'%')
                ->orWhereHas('produkKategori', function (Builder $qpk) use ($search){
                    $qpk
                    ->whereHas('masterKategori', function (Builder $qmpk) use ($search){
                        $qmpk
                        ->where('name', 'like', '%'.$search.'%'); 
                    });
                });
            })
            // #distribusi
            ->orWhereHas('distribusi', function (Builder $qd) use($search) {
                $qd
                ->where('is_archived', false)
                ->where(function (Builder $qdd) use($search){
                    $qdd
                    ->where('nama_retailer', 'like', '%'.$search.'%')
                    ->orWhere('kode_distribusi', 'like', '%'.$search.'%')
                    ->orWhere('alamat', 'like', '%'.$search.'%');
                });
            });
        }
    }

    public function master_produk() : BelongsTo
    {
        return $this->belongsTo(MasterProduk::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lot_penggunaan_logs(): HasMany 
    {
        return $this->hasMany(LotPenggunaanLogs::class);
    }

    public function distribusi(): HasOne
    {
        return $this->hasOne(Distribusi::class)->where('is_archived', false)->latest('id');
    }

    public function lokasi_penyimpanan(): BelongsTo
    {
        return $this->belongsTo(LokasiPenyimpanan::class);
    }

    protected static function booted()
    {
        static::retrieved(function($produksi) {
            if($produksi->is_archived == false){
                if($produksi->status == 'Pending'){
                    // dd($produksi->created_at);
                    if(parseDate($produksi->created_at, addDays: 1) < parseDate(now())){
                        $produksi->is_archived = true;
                    }
                }
            }
            if($produksi->isDirty('is_archived')) {
                $produksi->save();
                return back();
            };
            // if(lowercase($produksi->status) === 'pending' || lowercase($produksi->status) === 'proses' && $produksi->is_archived === false) {
            //     if(parseDate($produksi->created_at, addDays: 1) < parseDate(now())){
            //         delete
            //         dd(parseDate($produksi->created_at));
            //     }
            // }
        });
    }
}
