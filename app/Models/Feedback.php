<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Feedback extends Model
{
    use HasFactory;

    protected $table = 'feedback';
    protected $fillable = [
        'id_peserta',
        'comment',
        'status_kerja',
    ];

    public function peserta()
    {
        return $this->belongsTo(Peserta::class, 'id_peserta');
    }


}
