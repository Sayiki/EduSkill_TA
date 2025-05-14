<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Pelatihan extends Model
{
    use HasFactory;

    protected $table = 'pelatihan';

    protected $fillable = [
        'nama_pelatihan',
        'admin_id',
        'keterangan_pelatihan',
        'jumlah_kuota',
        'jumlah_peserta',
        'waktu_pengumpulan'
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

}
