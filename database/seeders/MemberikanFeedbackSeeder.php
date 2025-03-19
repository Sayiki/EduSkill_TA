<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MemberikanFeedback;
use App\Models\Peserta;
use Illuminate\Support\Str;


class MemberikanFeedbackSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    protected $table = 'memberikan_feedback'; 
    
    public function run(): void
    {
        // Ambil semua data peserta
        $peserta = Peserta::all();

        // Buat 3 data feedback
        MemberikanFeedback::create([
            'id_peserta' => $peserta[0]->id, // Ambil id peserta pertama
            'comment' => 'Pelatihan ini sangat bermanfaat!',
            'rating' => rand(1, 5),
        ]);

        MemberikanFeedback::create([
            'id_peserta' => $peserta[1]->id, // Ambil id peserta kedua
            'comment' => 'Instruktur sangat ramah dan membantu.',
            'rating' => rand(1, 5),
        ]);

        MemberikanFeedback::create([
            'id_peserta' => $peserta[2]->id, // Ambil id peserta ketiga
            'comment' => 'Materi kurang mendalam, bisa lebih baik lagi.',
            'rating' => rand(1, 5),
        ]);
    }
}
