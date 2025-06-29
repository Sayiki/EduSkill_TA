<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class InformasiKontak extends Model
{
    use HasFactory;
    protected $table = 'informasi_kontak';
    protected $fillable = ['alamat', 'email', 'telepon', 'admin_id','whatsapp','instagram','admin_id',];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
}
