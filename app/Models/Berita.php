<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Berita extends Model
{
    use HasFactory;
    protected $table = 'berita';
    protected $fillable = [
        'admin_id',
        'title',
        'deskripsi',
        'gambar',
        'date',
    ];

    protected $casts = [
        'date' => 'date:Y-m-d', // Casting ke objek Date Carbon dengan format Y-m-d
    ];

    /**
     * Mendapatkan admin yang membuat berita ini.
     * Asumsi: admin_id merujuk ke tabel 'admin' dan model Admin ada.
     */
    public function adminProfile()
    {
        // Sesuaikan 'Admin::class' dan 'admin_id' jika nama model/foreign key berbeda
        return $this->belongsTo(Admin::class, 'admin_id'); 
    }
}
