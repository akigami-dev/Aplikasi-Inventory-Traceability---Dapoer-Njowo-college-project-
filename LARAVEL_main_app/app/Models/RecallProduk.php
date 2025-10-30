<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecallProduk extends Model
{
    protected $fillable = [
        'distribusi_id',
        'kode_recall',
        'tanggal_recall',
        'jumlah_recall',
        'alasan_recall',
        'keterangan',
        'user_id',
        'is_archived',
    ];

    public static function getField(String $field)
    {
        switch ($field) {
            case 'created at':
                return 'created_at';
                break;
            case 'kode recall':
                return 'kode_recall';
                break;
            case 'tanggal recall':
                return 'tanggal_recall';
                break;
            case 'jumlah recall':
                return 'jumlah_recall';
                break;
            case 'alasan recall':
                return 'alasan_recall';
                break;
            case 'keterangan':
                return 'keterangan';
                break;
            
            default:
                return false;
                break;
        }
    }

    public function distribusi(): BelongsTo
    {
        return $this->belongsTo(Distribusi::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
