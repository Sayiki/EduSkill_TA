<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Admin;
use App\Models\Berita;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Berita>
 */
class BeritaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Mengambil ID admin secara acak. Jika tidak ada, buat admin baru.
            'admin_id' => Admin::inRandomOrder()->first()->id ?? Admin::factory(),
            'title' => $this->faker->sentence(6),
            'deskripsi' => $this->faker->realText(800),
            'gambar' => $this->faker->imageUrl(1280, 720, 'news', true),
            'date' => $this->faker->dateTimeThisYear(),
        ];
    }
}
