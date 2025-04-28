<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Peserta;
use App\Models\Pendidikan;
use App\Models\Admin;
use App\Models\Ketua;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 10 Peserta
        User::factory()->count(10)->peserta()->create()->each(function ($user) {
            Peserta::factory()->create([
                'user_id' => $user->id,
                'id_pendidikan' => Pendidikan::inRandomOrder()->value('id'),
            ]);
        });

        // 1 Admin
        $adminUser = User::factory()->admin()->create([
            'name' => 'Admin Guy',
            'email' => 'admin@example.com',
        ]);
        
        Admin::factory()->create([
            'user_id' => $adminUser->id,
        ]);
        

        // 1 Ketua
        $ketuaUser = User::factory()->ketua()->create([
            'name' => 'Ketua Boss',
            'email' => 'ketua@example.com',
        ]);
        
        Ketua::factory()->create([
            'user_id' => $ketuaUser->id,
        ]);
        
    }

}

