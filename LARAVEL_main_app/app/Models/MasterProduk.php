<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;


class MasterProduk extends Model
{
    protected $fillable = [
        'kode_produk',
        'nama_produk',
        'gambar_path',
        'harga',
        'berat_bersih',
        'satuan_berat',
        'template_label_path',
    ];

    public static function getField($field){
        switch ($field) {
            case 'created at':
                return 'created_at';
                break;
            case 'kode produk':
                return 'kode_produk';
                break;
            case 'nama produk':
                return 'nama_produk';
                break;
            case 'kategori':
                return 'kategori';
                break;
            case 'harga':
                return 'harga';
                break;
            case 'berat bersih':
                return 'berat_bersih';
                break;
            case 'satuan berat':
                return 'satuan_berat';
                break;
            default:
                return null;
                break;
        }
    }
    
    public static function getAll(string $search = '', int $paginate = 10, array $filter = [])
    {
        $data = MasterProduk::where('is_archived', false);

        if($search){
            $data = $data->where(function (Builder $query) use ($search){
                $query
                ->where('kode_produk', 'like', '%'.$search.'%')
                ->orWhere('nama_produk', 'like', '%'.$search.'%')
                // ->orWhere('kategori', 'like', '%'.$search.'%')
                ->orWhere('harga', 'like', '%'.$search.'%')
                ->orWhere('berat_bersih', 'like', '%'.$search.'%')
                ->orWhere('satuan_berat', 'like', '%'.$search.'%');
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

        $data = $data->with(['produkKategori', 'sertifikasiProduk'])->paginate($paginate);

        return $data;
    }

    public function sertifikasiProduk(): hasMany 
    {
        return $this->hasMany(SertifikasiProduk::class);
    }

    public function produkKategori(): HasMany
    {
        return $this->hasMany(ProdukKategori::class);
    }
}
