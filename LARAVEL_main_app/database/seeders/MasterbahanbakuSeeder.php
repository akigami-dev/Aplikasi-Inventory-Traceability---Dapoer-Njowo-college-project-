<?php

namespace Database\Seeders;

use App\Models\MasterBahanBaku;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasterbahanbakuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MasterBahanBaku::factory()->create([
            'kode_bahan' => 'BS0001',
        ]);
        MasterBahanBaku::factory()->create([
            'kode_bahan' => 'BS0002',
        ]);
        MasterBahanBaku::factory()->create([
            'kode_bahan' => 'BS0003',
        ]);
    }
}
