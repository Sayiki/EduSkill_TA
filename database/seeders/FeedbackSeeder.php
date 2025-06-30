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

        if (Pelatihan::count() === 0) {
            // You might need to adjust this to create valid mentor_id, admin_id etc.
            // For simplicity, let's assume PelatihanFactory handles its dependencies.
            Pelatihan::factory()->count(5)->create();
        }

        // Ensure DaftarPelatihan exists
        if (DaftarPelatihan::count() === 0) {
            // Create DaftarPelatihan, linking existing Peserta and Pelatihan
            DaftarPelatihan::factory()->count(20)->create(); // Create some registrations
        }

        Feedback::factory()->count(30)->create();
    }
}
