<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Notifikasi extends Model
{
    use HasFactory;

    protected $table = 'notifikasi';
    protected $primaryKey = 'id_notifikasi';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['pesan', 'status'];

}
