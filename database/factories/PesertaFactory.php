<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
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
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Peserta>
 */
class PesertaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => null,
            'nik_peserta' => $this->faker->numerify('################'),
            'jenis_kelamin' => $this->faker->randomElement(['Laki-laki', 'Perempuan']),
            'alamat_peserta' => $this->faker->address(),
            'id_pendidikan' => null,
        ];
    }
}
