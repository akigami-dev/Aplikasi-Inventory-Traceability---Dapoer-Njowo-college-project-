<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('master_produks')->insert([
            ['kode_produk' => 'PK001', 'nama_produk' => 'Kunyit Asam', 'gambar_path' => 'default.png', 'kategori' => 'jamu', 'harga' => 12000, 'created_at' => now(), 'updated_at' => now()],
            ['kode_produk' => 'PK002', 'nama_produk' => 'Beras Kencur', 'gambar_path' => 'default.png', 'kategori' => 'jamu', 'harga' => 15000, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
