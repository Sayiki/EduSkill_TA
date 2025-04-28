<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProfileYayasan extends Model
{
    use HasFactory;
    protected $table = 'profile_yayasan';
    protected $fillable = ['deskripsi_yayasan', 'nama_yayasan', 'foto_yayasan'];
}
