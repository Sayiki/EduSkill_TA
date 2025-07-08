<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Pelatihan;
use App\Models\Admin;

class KategoriPelatihan extends Model
{
    use HasFactory;

    // Define the table name if it's different from the plural of the model name
    protected $table = 'kategori_pelatihan';

    protected $fillable = [
        'admin_id',
        'nama_kategori',
    ];

    // Relationship to Pelatihan (One Category has Many Trainings)
    public function pelatihan()
    {
        return $this->hasMany(Pelatihan::class, 'kategori_id');
    }

    // Relationship to Admin
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
}