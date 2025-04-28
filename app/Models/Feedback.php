<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Feedback extends Model
{
    use HasFactory;

    protected $table = 'feedback';
    protected $fillable = ['nama_peserta', 'comment'];

    public function peserta()
    {
        return $this->belongsTo(Peserta::class, 'id_peserta');
    }


}
