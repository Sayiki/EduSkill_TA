<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            PendidikanSeeder::class,
            NotifikasiSeeder::class,
            FeedbackSeeder::class,
            LaporanAdminSeeder::class,
            PelatihanSeeder::class,
            DaftarPelatihanSeeder::class,
            StatusLamaranSeeder::class,
            InformasiGaleriSeeder::class,
            InformasiKontakSeeder::class,
            InformasiLembagaSeeder::class,
            ProfileYayasanSeeder::class,
            ProfileLKPSeeder::class,
            ProfileLPKSeeder::class,
        ]);
    }
}
