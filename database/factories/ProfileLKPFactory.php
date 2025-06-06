<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProfileLKP>
 */
class ProfileLKPFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'lembaga_id' => \App\Models\InformasiLembaga::factory(),
            'nama_lkp' => $this->faker->company(),
            'deskripsi_lkp' => $this->faker->paragraph(),
            'foto_lkp' => $this->faker->imageUrl()
        ];
    }
}
