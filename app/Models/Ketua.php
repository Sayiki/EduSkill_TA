<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Ketua extends Model
{
    use HasFactory;

    protected $table = 'ketua';
    protected $keyType = 'string';

    protected $fillable = [
        'username',
        'password',
    ];


}
