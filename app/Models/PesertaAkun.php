<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PesertaAkun extends Model
{
    use HasFactory;

    protected $table = 'peserta_akun';
    protected $primaryKey = 'id_peserta';
    public $incrementing = false; // UUID is not auto-incrementing
    protected $keyType = 'string'; // UUID is a string

    protected $fillable = [
        'nama_peserta',
        'nik_peserta',
        'alamat_peserta',
        'jenis_kelamin',
        'pendidikan_peserta',
    ];

}
