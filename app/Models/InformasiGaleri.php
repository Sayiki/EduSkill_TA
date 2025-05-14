<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class InformasiGaleri extends Model
{
    use HasFactory;
    protected $table = 'informasi_galeri';
    protected $fillable = ['nama_kegiatan', 'foto_galeri', 'admin_id'];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
}
