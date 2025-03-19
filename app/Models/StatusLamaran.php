<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class StatusLamaran extends Model
{
    use HasFactory;

    protected $table = 'status_lamarans';
    protected $primaryKey = 'id_peserta';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id_peserta',
        'nama_pelatihan',
        'status',
    ];

}
