<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Admin;
use App\Models\Banner;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Banner>
 */
class BannerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'admin_id' => Admin::inRandomOrder()->first()->id ?? Admin::factory(),
            'nama_banner' => $this->faker->words(3, true),
            'gambar' => $this->faker->imageUrl(1920, 600, 'business', true),
        ];
    }
}
