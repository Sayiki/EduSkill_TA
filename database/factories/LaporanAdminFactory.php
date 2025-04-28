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
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LaporanAdmin>
 */
class LaporanAdminFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'waktu_upload' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'jumlah_peserta' => $this->faker->numberBetween(50, 200),
            'jumlah_lulusan_bekerja' => $this->faker->numberBetween(10, 100),
            'jumlah_pendaftar' => $this->faker->numberBetween(100, 300),
            'pelatihan_dibuka' => $this->faker->sentence(3),
            'pelatihan_berjalan' => $this->faker->sentence(3),
        ];
    }
}
