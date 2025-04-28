<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class InformasiLembaga extends Model
{
    use HasFactory;
    protected $table = 'informasi_lembaga';
    protected $fillable = ['visi', 'misi'];
}
