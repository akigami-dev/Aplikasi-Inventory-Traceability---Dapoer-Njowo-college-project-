<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // role::factory()->create(
        //     [
        //         ['nama_role' => 'admin'],
        //         ['nama_role' => 'user'],
        //         ['nama_role' => 'guest'],
        //     ]
        // );
        DB::table('roles')->insert(
            [
                ['nama_role' => 'owner', 'created_at' => now(), 'updated_at' => now()],
                ['nama_role' => 'staf admin', 'created_at' => now(), 'updated_at' => now()],
                ['nama_role' => 'staf produksi', 'created_at' => now(), 'updated_at' => now()],
                ['nama_role' => 'guest', 'created_at' => now(), 'updated_at' => now()],
            ]
        );
    }
}
