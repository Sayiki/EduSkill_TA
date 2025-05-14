<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LaporanAdmin extends Model
{
    use HasFactory;

    protected $table = 'laporan_admin';
    protected $keyType = 'string';

    protected $fillable = [
        'admin_id',
        'jumlah_peserta',
        'jumlah_lulusan',
        'jumlah_pendaftar',
        'pelatihan_dibuka',
        'pelatihan_berjalan',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }


}
