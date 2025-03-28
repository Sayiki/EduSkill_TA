<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Informasi extends Model
{
    use HasFactory;

    protected $table = 'informasi';
    protected $primaryKey = 'id_informasi';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['isi_informasi', 'kategori', 'foto_informasi'];


}
