<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Ketua;
use App\Models\Admin; 
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InformasiKontak>
 */
class InformasiKontakFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'alamat' => $this->faker->address(),
            'email' => $this->faker->safeEmail(),
            'telepon' => $this->faker->phoneNumber(),
            'admin_id' => Admin::inRandomOrder()->first()->id,
        ];
    }
}
