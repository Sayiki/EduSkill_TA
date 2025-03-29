<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('daftar_pelatihan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_peserta')->constrained('peserta')->onDelete('cascade'); // untuk ambil data nama_peserta, nik_peserta, alamat_peserta, pendidikan peserta
            $table->foreignId('id_pelatihan')->constrained('pelatihan')->onDelete('cascade'); // untuk pilih pelatihan
            $table->string('kk')->nullable(); 
            $table->string('ktp')->nullable();
            $table->string('ijazah')->nullable();
            $table->string('foto')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daftar_pelatihan');
    }
};
