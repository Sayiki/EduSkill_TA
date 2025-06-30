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
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Feedback>
 */
class FeedbackFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $daftarPelatihan = DaftarPelatihan::inRandomOrder()->first();
        if (!$daftarPelatihan) {
            $daftarPelatihan = DaftarPelatihan::factory()->create();
        }

        return [
            'peserta_id' => Peserta::inRandomOrder()->first()->id ?? Peserta::factory(),
            'daftar_pelatihan_id' => $daftarPelatihan->id,
            'comment' => substr($this->faker->sentence(10, true), 0, 100),
            'tempat_kerja' => $this->faker->randomElement(['janitor', 'worker', 'hacker', 'jonkler']),

        ];
    }
}
