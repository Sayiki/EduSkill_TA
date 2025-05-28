<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Slideshow;
use App\Models\Admin;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Slideshow>
 */
class SlideshowFactory extends Factory
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
            'nama_slide' => $this->faker->words(4, true),
            'gambar' => $this->faker->imageUrl(1920, 1080, 'nature', true),
        ];
    }
}
