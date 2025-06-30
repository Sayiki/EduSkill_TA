<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Feedback extends Model
{
    use HasFactory;

    protected $table = 'feedback';
    protected $fillable = [
        'peserta_id',
        'daftar_pelatihan_id',
        'comment',
        'tempat_kerja',
        'status',
    ];

    public function peserta()
    {
        return $this->belongsTo(Peserta::class, 'peserta_id');
    }

    public function daftarPelatihan() // Relasi baru
    {
        return $this->belongsTo(DaftarPelatihan::class, 'daftar_pelatihan_id');
    }


}
