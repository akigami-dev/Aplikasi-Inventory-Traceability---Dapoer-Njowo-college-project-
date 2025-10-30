<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SertifikasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sertifikasis')->insert([
            ['nama_sertifikasi' => 'HALAL', 'badan_penerbit' => 'MUI', 'logo_path' => 'default.png', 'created_at' => now(), 'updated_at' => now()],
            ['nama_sertifikasi' => 'BPOM', 'badan_penerbit' => 'BPOM', 'logo_path' => 'default.png', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
