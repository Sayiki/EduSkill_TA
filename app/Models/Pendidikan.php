<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Pendidikan extends Model
{
    use HasFactory;

    protected $table = 'pendidikan';

    protected $fillable = ['nama_pendidikan'];
}
