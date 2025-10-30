<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class LokasiPenyimpanan extends Model
{
    use SoftDeletes;
    protected $table = 'lokasi_penyimpanans';
    protected $fillable = [
        'name',
        'deskripsi',
        'suhu_default',
    ];

    protected $hidden = [
        'deleted_at',
    ];

    // Cast
    public function scopeSearch($query, string $search = '')
    {
        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%")
                  ->orWhere('suhu_default', 'like', "%{$search}%");
        }
    }

    public static function getField(string $field)
    {
        switch ($field) {
            case 'created at':
                return 'created_at';
                break;
            case 'name':
                return 'name';
                break;
            case 'lokasi penyimpanan':
                return 'name';
                break;
            case 'deskripsi':
                return 'deskripsi';
                break;
            case 'suhu default':
                return 'suhu_default';
                break;
            default:
                return false;
                break;
        }
    }

    public static function getAll(string $search = '', int $paginate = 10, array $filter = []) 
    {
        $data = LokasiPenyimpanan::query();

        if ($search) {
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

        $data = $data->paginate($paginate);
        return $data;

    }

    public static function addRecord(string $name, string $deskripsi, float $suhu_default)
    {
        try{
            return self::create([
                'name' => $name,
                'deskripsi' => $deskripsi,
                'suhu_default' => $suhu_default,
            ]);
        } catch (\Throwable $th) {
            dd($th);
            return false;
        }
    }

    public static function updateRecord(string $name, string $deskripsi, float $suhu_default, LokasiPenyimpanan $penyimpanan)
    {
        try{
            return $penyimpanan->update([
                'name' => $name,
                'deskripsi' => $deskripsi,
                'suhu_default' => $suhu_default,
            ]);
        } catch (\Throwable $th) {
            dd($th);
            return false;
        }
    }

    public function produksi(): HasMany
    {
        return $this->hasMany(Produksi::class);
    }
}
