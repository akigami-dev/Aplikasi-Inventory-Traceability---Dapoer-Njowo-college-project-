<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $name = "Admin";
        $email = "admin@gmail.com";
        $password = "admin";
        User::factory()->create([
            'name' => $name,
            'email' => $email,
            'password' => bcrypt($password),
            'role_id' => 1
        ]);
    }
}
