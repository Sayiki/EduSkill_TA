<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LaporanAdmin extends Model
{
    use HasFactory;

    protected $table = 'laporan_admin';
    protected $primaryKey = 'id_laporan';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'jumlah_peserta',
        'jumlah_lulusan',
        'jumlah_pendaftar',
        'pelatihan_dibuka',
        'pelatihan_berjalan',
    ];


}
