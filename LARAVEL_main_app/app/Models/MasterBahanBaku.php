<?php

namespace App\Models;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MasterBahanBaku extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_bahan',
        'nama_bahan',
    ];

    public static function getField(String $field)
    {
        switch ($field) {
            case 'created at':
                return 'created_at';
                break;
            case 'kode bahan':
                return 'kode_bahan';
                break;
            case 'nama bahan':
                return 'nama_bahan';
                break;
            default:
                return false;
                break;
        }
    }

    public static function getAll(string $search = '', int $paginate = 10, array $filter = [])
    {
        $data = MasterBahanBaku::where('is_archived', false);
        if($search){
            $data->where(function (Builder $query) use ($search){
                $query
                ->where('kode_bahan', 'like', '%'.$search.'%')
                ->orWhere('nama_bahan', 'like', '%'.$search.'%');
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

    public function supplier() : BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }
}
