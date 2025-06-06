<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProfileLPK>
 */
class ProfileLPKFactory extends Factory
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
            'nama_lpk' => $this->faker->company(),
            'deskripsi_lpk' => $this->faker->paragraph(),
            'foto_lpk' => $this->faker->imageUrl()
        ];
    }
}
