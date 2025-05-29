<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;
    protected $table = 'banner';
    protected $fillable = [
        'admin_id',
        'nama_banner',
        'gambar',
    ];
    public function adminProfile() 
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
}
