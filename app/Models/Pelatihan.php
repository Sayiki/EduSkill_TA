<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelatihan extends Model
{
    use HasFactory;

    protected $table = 'pelatihan';

    protected $fillable = [
        'admin_id',         // Admin yang membuat/mengelola pelatihan
        'mentor_id',        // Mentor untuk pelatihan ini
        'nama_pelatihan',
        'kategori',
        'biaya',
        'keterangan_pelatihan',
        'jumlah_kuota',
        'jumlah_peserta',   // Jumlah peserta yang sudah diterima
        'waktu_pengumpulan',// Bisa jadi deadline pendaftaran atau waktu mulai
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'waktu_pengumpulan' => 'datetime',
        'jumlah_kuota' => 'integer',
        'jumlah_peserta' => 'integer',
    ];

    /**
     * Mendapatkan admin yang mengelola pelatihan ini.
     */
    public function admin() // Nama relasi bisa disesuaikan jika sudah ada
    {
        // Asumsi admin_id merujuk ke tabel admin dan model Admin ada
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    /**
     * Mendapatkan mentor untuk pelatihan ini.
     */
    public function mentor()
    {
        return $this->belongsTo(Mentor::class, 'mentor_id');
    }

    /**
     * Mendapatkan daftar peserta yang mendaftar pelatihan ini.
     */
    public function pendaftar()
    {
        return $this->hasMany(DaftarPelatihan::class, 'pelatihan_id');
    }
}
