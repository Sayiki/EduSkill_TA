<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Peserta;
use App\Models\Pelatihan;
use App\Models\DaftarPelatihan;
use App\Models\StatusLamaran;

class StatusLamaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure related records exist
        if (Peserta::count() === 0) {
            Peserta::factory(10)->create();
        }

        if (Pelatihan::count() === 0) {
            Pelatihan::factory(5)->create();
        }

        if (DaftarPelatihan::count() === 0) {
            DaftarPelatihan::factory(15)->create();
        }

        StatusLamaran::factory(30)->create();
    }
}
