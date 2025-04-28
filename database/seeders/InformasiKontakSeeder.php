<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\InformasiKontak;

class InformasiKontakSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        InformasiKontak::factory()->count(1)->create();
    }
}
