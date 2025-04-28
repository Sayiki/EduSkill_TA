<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
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

class FeedbackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Peserta::count() === 0) {
            Peserta::factory()->count(10)->create();
        }

        Feedback::factory()->count(30)->create();
    }
}
