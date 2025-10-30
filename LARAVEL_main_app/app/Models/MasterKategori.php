<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterKategori extends Model
{
    use SoftDeletes;
    protected $table = 'master_kategoris';
    protected $fillable = ['name', 'deskripsi'];

    public function scopeSearch($query, $search = '')
    {
        if($search) {
            $query
                ->where('name', 'like', '%'.$search.'%')
                ->orWhere('deskripsi', 'like', '%'.$search.'%');
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
            case 'nama kategori':
                return 'name';
                break;
            case 'deskripsi':
                return 'deskripsi';
                break;
            default:
                return false;
                break;
        }
    }

    public static function getAll(string $search = '', int $paginate = 10, array $filter = [])
    {
        $data = MasterKategori::query();
        
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



    public function produkKategori() : HasMany
    {
        return $this->hasMany(ProdukKategori::class);
    }
}
