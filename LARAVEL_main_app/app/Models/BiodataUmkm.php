<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BiodataUmkm extends Model
{
    protected $table = 'biodata_umkms';
    protected $fillable =[
        'nama',
        'alamat',
        'no_telpon',
        'email',
    ];
}
