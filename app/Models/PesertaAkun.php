<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PesertaAkun extends Model
{
    use HasFactory;

    protected $table = 'peserta_akun';
    protected $keyType = 'string'; 

    protected $fillable = [
        'id_peserta',
        'nama_peserta',
        'nik_peserta',
        'alamat_peserta',
        'jenis_kelamin',
        'pendidikan_peserta',
    ];

    public function peserta()
    {
        return $this->belongsTo(Peserta::class, 'id_peserta');
    }


}
