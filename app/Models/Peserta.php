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
        'nik_peserta', 
        'jenis_kelamin',
        'alamat_peserta', 
        'id_pendidikan',
        'nomor_telp',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function pendidikan()
    {
        return $this->belongsTo(Pendidikan::class, 'id_pendidikan');
    }

}
