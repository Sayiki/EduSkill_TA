<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Peserta;
use App\Models\Pendidikan;
use App\Models\Admin;
use App\Models\Ketua;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 10 Peserta
        User::factory()->count(9)->peserta()->create()->each(function ($user) {
            Peserta::factory()->create([
                'user_id' => $user->id,
                'pendidikan_id' => Pendidikan::inRandomOrder()->value('id'),
            ]);
        });

        // 1 Admin
        $adminUser = User::factory()->admin()->create([
            'name' => 'Admin Guy',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);
        
        Admin::factory()->create([
            'user_id' => $adminUser->id,
        ]);

        // 1 Peserta
        $adminUser = User::factory()->admin()->create([
            'name' => 'peserta',
            'username' => 'peserta',
            'email' => 'peserta@example.com',
            'password' => Hash::make('password'),
        ]);
        
        Admin::factory()->create([
            'user_id' => $adminUser->id,
        ]);
        

        // 1 Ketua
        $ketuaUser = User::factory()->ketua()->create([
            'name' => 'Ketua Boss',
            'username' => 'ketua',
            'email' => 'ketua@example.com',
            'password' => Hash::make('password'),
        ]);
        
        Ketua::factory()->create([
            'user_id' => $ketuaUser->id,
        ]);
        
    }

}

