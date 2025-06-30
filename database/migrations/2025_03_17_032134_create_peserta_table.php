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
        Schema::create('peserta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); 
            $table->string('foto_peserta')->nullable();
            $table->string('nik_peserta', 100)->nullable();
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan'])->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('alamat_peserta', 1000)->nullable();
            $table->string('nomor_telp')->nullable();
            $table->enum('status_lulus', ['Lulus', 'Belum Lulus'])->default('Belum Lulus');
            $table->unsignedBigInteger('pendidikan_id')->nullable();
            $table->timestamps();
        
            $table->foreign('pendidikan_id')->references('id')->on('pendidikan')->onDelete('set null');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peserta');
    }
};
