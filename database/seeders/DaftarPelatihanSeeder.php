<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Peserta;
use App\Models\Pelatihan;
use App\Models\DaftarPelatihan;


class DaftarPelatihanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure Peserta and Pelatihan exist first
        if (Peserta::count() === 0) {
            Peserta::factory(40)->create();
        }

        if (Pelatihan::count() === 0) {
            Pelatihan::factory(40)->create();
        }

        DaftarPelatihan::factory(40)->create();
    }
}
