<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProfileYayasan>
 */
class ProfileYayasanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'deskripsi_yayasan' => $this->faker->paragraph(),
            'nama_yayasan' => $this->faker->company(),
            'foto_yayasan' => $this->faker->imageUrl()
        ];
    }
}
