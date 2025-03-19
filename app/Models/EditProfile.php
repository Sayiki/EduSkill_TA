<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EditProfile extends Model
{
    use HasFactory;

    protected $table = 'edit_profile';
    protected $primaryKey = 'id_peserta';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id_peserta',
        'nama_peserta',
        'nik_peserta',
        'alamat_peserta',
        'jenis_kelamin',
        'pendidikan_peserta',
    ];

}
