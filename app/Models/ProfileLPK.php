<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProfileLPK extends Model
{
    use HasFactory;
    protected $table = 'profile_lpk';
    protected $fillable = ['lembaga_id', 'nama_lpk', 'deskripsi_lpk', 'foto_lpk'];

    public function lembaga()
    {
        return $this->belongsTo(InformasiLembaga::class, 'lembaga_id');
    }
}
