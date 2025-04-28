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
use Illuminate\Support\Facades\Hash;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ketua>
 */
class KetuaFactory extends Factory
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
        ];
    }
}
