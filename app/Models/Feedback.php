<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Feedback extends Model
{
    use HasFactory;

    protected $table = 'feedback';
    protected $primaryKey = 'id_feedback';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['nama_peserta', 'comment'];


}
