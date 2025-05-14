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
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pelatihan>
 */
class PelatihanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_pelatihan' => $this->faker->sentence(3),
            'keterangan_pelatihan' => $this->faker->text(200),
            'jumlah_kuota' => $this->faker->numberBetween(20, 100),
            'jumlah_peserta' => $this->faker->numberBetween(0, 20),
            'waktu_pengumpulan' => $this->faker->dateTimeBetween('+1 week', '+1 month'),
            'admin_id' => Admin::inRandomOrder()->first()->id,
        ];
    }
}
