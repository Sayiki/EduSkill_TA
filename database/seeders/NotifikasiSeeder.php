<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Peserta;
use App\Models\Notifikasi;

class NotifikasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if there are Peserta records
        if (Peserta::count() === 0) {
            Peserta::factory()->count(10)->create();
        }

        Notifikasi::factory()->count(50)->create();
    }
}
