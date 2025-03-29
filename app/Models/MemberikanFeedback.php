<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class MemberikanFeedback extends Model
{
    use HasFactory;

    protected $table = 'memberikan_feedback';
    protected $fillable = [
        'id_peserta',
        'comment',
        'rating',
    ];

}
