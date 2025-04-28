<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'peran'];

    public function peserta()
    {
        return $this->hasOne(Peserta::class, 'user_id');
    }

    public function admin()
    {
        return $this->hasOne(Admin::class, 'user_id');
    }

    public function ketua()
    {
        return $this->hasOne(Ketua::class, 'user_id');
    }
}
