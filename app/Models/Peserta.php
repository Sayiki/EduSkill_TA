<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model; 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Str;

class Peserta extends Model
{
    use HasFactory;

    protected $table = 'peserta';

    protected $fillable = [
        'user_id',
        'foto_peserta',
        'nik_peserta',
        'jenis_kelamin',
        'tanggal_lahir', 
        'alamat_peserta',
        'nomor_telp',
        'status_lulus',
        'status_kerja',
        'pendidikan_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function pendidikan()
    {
        return $this->belongsTo(Pendidikan::class, 'pendidikan_id');
    }

    public function feedback()
    {
        return $this->hasOne(Feedback::class, 'peserta_id');
    }

}
