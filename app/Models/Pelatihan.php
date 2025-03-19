<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Pelatihan extends Model
{
    use HasFactory;

    protected $table = 'pelatihan';
    protected $primaryKey = 'id_pelatihan';
    public $incrementing = false; // UUID is not auto-incrementing
    protected $keyType = 'string'; // UUID is a string

    protected $fillable = [
        'nama_pelatihan',
        'keterangan_pelatihan',
        'jumlah_kuota',
        'jumlah_peserta',
        'waktu_pengumpulan'
    ];

}
