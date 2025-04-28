<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PendidikanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('pendidikan')->insert([
            ['nama_pendidikan' => 'SMP'],
            ['nama_pendidikan' => 'SMA'],
            ['nama_pendidikan' => 'SMK'],
            ['nama_pendidikan' => 'Kuliah'],
        ]);
    }
}
