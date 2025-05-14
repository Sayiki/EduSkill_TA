<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class InformasiLembaga extends Model
{
    use HasFactory;
    protected $table = 'informasi_lembaga';
    protected $fillable = ['visi', 'misi', 'admin_id'];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
}
