<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProfileLKP extends Model
{
    use HasFactory;
    protected $table = 'profile_lkp';
    protected $fillable = ['id_lembaga', 'nama_lkp', 'deskripsi_lkp', 'foto_lkp'];

    public function lembaga()
    {
        return $this->belongsTo(InformasiLembaga::class, 'id_lembaga');
    }
}
