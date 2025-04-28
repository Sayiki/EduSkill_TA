<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InformasiLembaga>
 */
class InformasiLembagaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'visi' => $this->faker->sentence(),
            'misi' => $this->faker->paragraph()
        ];
    }
}
