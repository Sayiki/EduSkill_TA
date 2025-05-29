<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Slideshow extends Model
{
    use HasFactory;
    protected $table = 'slideshow';
    protected $fillable = [
        'admin_id',
        'nama_slide',
        'gambar',
    ];

    public function adminProfile() 
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
}
