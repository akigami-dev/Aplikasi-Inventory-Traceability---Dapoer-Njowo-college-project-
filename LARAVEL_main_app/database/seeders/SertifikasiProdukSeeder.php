<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SertifikasiProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sertifikasi_produks')->insert([
            ['master_produk_id' => 1, 'sertifikasi_id' => 2, 'kode_sertifikasi' => '5201923925598', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
