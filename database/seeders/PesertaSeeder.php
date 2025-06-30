<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Peserta;
use App\Models\User;
use App\Models\Pendidikan;
use Illuminate\Support\Str;


class PesertaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->count(60)->state(['peran' => 'peserta'])->create()->each(function ($user) {
            Peserta::factory()->create([
                'user_id' => $user->id,
                'pendidikan_id' => Pendidikan::inRandomOrder()->first()->id,
                'status_lulus' => 'belum dinilai',
            ]);
        });
    }
}
