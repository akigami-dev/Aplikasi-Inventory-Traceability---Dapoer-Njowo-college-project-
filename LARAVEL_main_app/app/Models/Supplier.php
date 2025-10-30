<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_supplier',
        'nama_supplier',
        'no_telpon',
        'alamat',
        'email',
        'is_archived',
    ];

    public function scopeSearch($query, string $search = '')
    {
        if ($search) {
            $query
                ->where('kode_supplier', 'like', '%'.$search.'%')
                ->orWhere('nama_supplier', 'like', '%'.$search.'%')
                ->orWhere('no_telpon', 'like', '%'.$search.'%')
                ->orWhere('alamat', 'like', '%'.$search.'%')
                ->orWhere('email', 'like', '%'.$search.'%');
        }
    }

    public static function getField(string $field)
    {
        switch ($field) {
            case 'created at':
                return 'created_at';
                break;
            case 'kode supplier':
                return 'kode_supplier';
                break;
            case 'nama supplier':
                return 'nama_supplier';
                break;
            case 'no telpon':
                return 'no_telpon';
                break;
            case 'alamat':
                return 'alamat';
                break;
            case 'email':
                return 'email';
                break;
            default:
                return false;
                break;
        }
    }

    public static function getAll(string $search = '', int $paginate = 10, array $filter = []) 
    {
        $data = Supplier::where('is_archived', false);

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
}
