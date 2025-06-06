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
        'kk',
        'ktp',
        'ijazah',
        'foto',
    ];

    public function peserta()
    {
        return $this->belongsTo(Peserta::class, 'id_peserta');
    }

    public function pelatihan()
    {
        return $this->belongsTo(Pelatihan::class, 'id_pelatihan');
    }
}
