<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class MemberikanFeedback extends Model
{
    use HasFactory;

    protected $table = 'memberikan_feedback';
    protected $primaryKey = 'id_peserta';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id_peserta',
        'comment',
        'rating',
    ];

}
