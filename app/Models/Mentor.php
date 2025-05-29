<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mentor extends Model
{
    use HasFactory;
    protected $table = 'mentor';
    protected $fillable = [
        'admin_id',
        'nama_mentor',
        'spesialisasi',
        'deskripsi_singkat',
        'foto_mentor',
    ];

    public function adminProfile() 
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    // Relasi ke Pelatihan (seorang mentor bisa memiliki banyak pelatihan)
    public function pelatihan()
    {
        return $this->hasMany(Pelatihan::class, 'mentor_id');
    }
}
