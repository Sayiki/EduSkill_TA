<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Peserta;
use App\Models\Pelatihan; // Import Pelatihan
use App\Models\DaftarPelatihan; // Import DaftarPelatihan
use App\Models\Feedback;

class FeedbackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure Peserta exists
        if (Peserta::count() === 0) {
            Peserta::factory()->count(10)->create();
        }

        // Ensure Pelatihan exists (if DaftarPelatihan needs it)
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

        // Now create Feedback, which will use the existing DaftarPelatihan records
        Feedback::factory()->count(30)->create();
    }
}