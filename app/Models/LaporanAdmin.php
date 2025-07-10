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
        'laporan_file',
        'laporan_deskripsi'
    ];

    protected $casts = [
        'created_at' => 'datetime',   // Eloquent biasanya menangani ini, tapi menambahkannya tidak masalah
        'updated_at' => 'datetime',   // Sama seperti created_at
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }


}
