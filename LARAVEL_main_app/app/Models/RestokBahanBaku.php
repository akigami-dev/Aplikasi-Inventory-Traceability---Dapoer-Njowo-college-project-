<?php

namespace App\Models;

use App\Models\LotBahanBaku;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RestokBahanBaku extends Model
{
    protected $fillable = [
        'kode_restok',
        'master_bahan_baku_id',
        'jumlah_restok',
        'tanggal_restok',
        'is_archived',
    ];

    public function scopeSearch($query, $search = '')
    {
        if($search) {
            $query
                ->where('kode_restok', 'like', '%'.$search.'%')
                ->orWhere('jumlah_restok', 'like', '%'.$search.'%')
                ->orWhere(function (Builder $qdate) use ($search) {
                    $qdate
                    ->whereYear('tanggal_restok', $search)
                    ->orWhereMonth('tanggal_restok', $search)
                    ->orWhereDay('tanggal_restok', $search)
                    ->orWhereTime('tanggal_restok', '>', $search);
                })
                ->orWhereHas('lot_bahan_baku', function (Builder $qb) use($search) {
                    $qb
                    ->where('kode_lot', 'like', '%'.$search.'%');
                });
        }
    }

    public static function getField(String $field){
        switch ($field) {
            case 'created at':
                return 'created_at';
                break;
            case 'kode restok':
                return 'kode_restok';
                break;
            case 'jumlah restok':
                return 'jumlah_restok';
                break;
            case 'tanggal restok':
                return 'tanggal_restok';
                break;
            
            default:
                return false;
                break;
        }
    }

    public static function getAll(string $search = '', int $paginate = 10, array $filter = [])
    {
        $data = RestokBahanBaku::where('is_archived', false);
        if($search) {
            $data->search($search);
        }
        // FILTERS
        if(isset($filter['sort'])){
            $sort = $filter['sort'];
            $field = self::getField($sort['field']);
            $order = $sort['order'];
            if($field){
                $data = $data->orderBy($field, $order);
            }
        }else{
            $data = $data->orderBy('created_at', 'desc');
        }

        if(isset($filter['range'])){
            $range = $filter['range'];
            $start = Carbon::parse($range['time'][0])->startOfDay();
            $end = Carbon::parse($range['time'][1])->endOfDay();
            $field = self::getField($range['field']);
            if($field){
                $data = $data->whereBetween($field, [$start, $end]);
            }
        }

        return $data->with(['master_bahan_baku', 'lot_bahan_baku'])->paginate($paginate);
    }

    public function lot_bahan_baku() : HasOne
    {
        return $this->hasOne(LotBahanBaku::class);
    }

    public function master_bahan_baku() : BelongsTo
    {
        return $this->belongsTo(MasterBahanBaku::class);
    }

    protected static function booted()
    {
        static::retrieved(function($restok_bahan_baku) {
            if($restok_bahan_baku->jumlah_restok !== $restok_bahan_baku->lot_bahan_baku->jumlah) {
                $restok_bahan_baku->status = 'used';
            }

            if($restok_bahan_baku->isDirty('status')){
                $restok_bahan_baku->save();
            }
        });
    }
}
