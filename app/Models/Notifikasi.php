<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Notifikasi extends Model
{
    use HasFactory;

    protected $table = 'notifikasi';
    protected $primaryKey = 'id';
    protected $fillable = ['id_peserta','pesan', 'status'];
    public $timestamps = true;

    public function peserta()
    {
        return $this->belongsTo(Peserta::class, 'id_peserta');
    }
}
