<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class StatusLamaran extends Model
{
    use HasFactory;

    protected $table = 'status_lamaran';
    protected $fillable = [
        'id_peserta',
        'nama_pelatihan',
        'status',
    ];

}
