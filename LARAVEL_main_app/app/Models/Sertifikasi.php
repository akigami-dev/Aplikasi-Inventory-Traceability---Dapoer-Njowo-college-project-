<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sertifikasi extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'nama_sertifikasi',
        'badan_penerbit',
        'kode_sertifikasi',
        'logo_path',
    ];

    public static function getField(string $field)
    {
        switch ($field) {
            case 'created at':
                return 'created_at';
                break;
            case 'nama sertifikasi':
                return 'nama_sertifikasi';
                break;
            case 'badan penerbit':
                return 'badan_penerbit';
                break;
            case 'kode sertifikasi':
                return 'kode_sertifikasi';
                break;
            default:
                return false;
                break;
        }
    }

    public static function getAll(string $search = '', int $paginate = 10, array $filter = []) 
    {
        $data = Sertifikasi::query();

        if ($search) {
            $data->where(function (Builder $query) use ($search) {
                $query
                ->where('nama_sertifikasi', 'like', '%'.$search.'%')
                ->orWhere('badan_penerbit', 'like', '%'.$search.'%')
                ->orWhere('kode_sertifikasi', 'like', '%'.$search.'%');
            });
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

        $data = $data->paginate($paginate);
        return $data;

    }

    public function masterProduk(): BelongsTo
    {
        return $this->BelongsTo(MasterProduk::class);
    }
    
    public function sertifikasiProduks(): HasMany
    {
        return $this->HasMany(SertifikasiProduk::class);
    }
}
