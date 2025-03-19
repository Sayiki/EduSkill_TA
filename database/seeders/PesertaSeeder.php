<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Peserta;
use Illuminate\Support\Str;


class PesertaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat 5 data peserta dummy
        for ($i = 1; $i <= 5; $i++) {
            Peserta::create([
                'nama_peserta' => 'Peserta ' . $i,
                'username' => 'peserta' . $i,
                'password' => 'password' . $i, // Password will be hashed by the model's mutator
            ]);
        }
    }
}
