<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Admin;
use App\Models\Mentor;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Mentor>
 */
class MentorFactory extends Factory
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
            'nama_mentor' => $this->faker->name(),
        ];
    }
}
