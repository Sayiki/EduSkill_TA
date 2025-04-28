<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Ketua extends Model
{
    use HasFactory;

    protected $table = 'ketua';

    protected $fillable = ['user_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
