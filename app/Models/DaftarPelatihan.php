<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DaftarPelatihan extends Model
{
    use HasFactory;

    protected $table = 'daftar_pelatihan';

    protected $fillable = [
        'peserta_id',
        'pelatihan_id',
        'status',
        'kk',
        'ktp',
        'ijazah',
        'foto',
    ];

    public function peserta()
    {
        return $this->belongsTo(Peserta::class, 'peserta_id');
    }

    public function pelatihan()
    {
        return $this->belongsTo(Pelatihan::class, 'pelatihan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function feedback() // Relasi baru ke Feedback
    {
        return $this->hasOne(Feedback::class, 'daftar_pelatihan_id');
    }
}
