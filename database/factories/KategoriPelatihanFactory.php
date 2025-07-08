<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Admin;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\KategoriPelatihan>
 */
class KategoriPelatihanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Assign a random, existing admin's ID
            'admin_id' => Admin::inRandomOrder()->first()?->id ?? Admin::factory()->create()->id,
            
            // Create a unique category name using a random word
            'nama_kategori' => $this->faker->unique()->word(),
        ];
    }
}
