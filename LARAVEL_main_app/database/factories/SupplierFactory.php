<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Supplier>
 */
class SupplierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kode_supplier' => fake()->randomNumber(),
            'nama_supplier' => fake()->name(),
            'no_telpon' => fake()->phoneNumber(),
            'alamat' => fake()->address(),
            'email' => fake()->email(),
        ];
    }
}
