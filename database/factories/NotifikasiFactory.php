<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Peserta;
use App\Models\Notifikasi;
use App\Models\Feedback;
use App\Models\Informasi;
use App\Models\Admin;
use App\Models\LaporanAdmin;
use App\Models\Ketua;
use App\Models\Pelatihan;
use App\Models\DaftarPelatihan;
use App\Models\StatusLamaran;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Notifikasi>
 */
class NotifikasiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_peserta' => Peserta::inRandomOrder()->first()->id ?? Peserta::factory(),
            'pesan' => $this->faker->sentence(6, true),
            'status' => $this->faker->randomElement(['belum dibaca', 'dibaca']),
        ];
    }
}
