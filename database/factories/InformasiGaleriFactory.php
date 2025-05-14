<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin;

class InformasiGaleriFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nama_kegiatan' => $this->faker->sentence(3),
            'foto_galeri' => $this->faker->imageUrl(640, 480, 'events', true),
            'admin_id' => Admin::inRandomOrder()->first()->id

        ];
    }
}
