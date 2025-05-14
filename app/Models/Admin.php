<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Admin extends Model
{
    use HasFactory;

    protected $table = 'admin';

    protected $fillable = ['user_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function laporanadmin()
    {
        return $this->hasMany(LaporanAdmin::class, 'admin_id');
    }

    public function informasigaleri()
    {
        return $this->hasMany(InformasiGaleri::class, 'admin_id');
    }

    public function informasikontak()
    {
        return $this->hasMany(InformasiKontak::class, 'admin_id');
    }

    public function informasilembaga()
    {
        return $this->hasMany(InformasiLembaga::class, 'admin_id');
    }

    public function profileyayasan()
    {
        return $this->hasMany(ProfileYayasan::class, 'admin_id');
    }

    public function pelatihan()
    {
        return $this->hasMany(Pelatihan::class, 'admin_id');
    }


}