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
            $table->string('nama_peserta', 100);
            $table->string('username', 100)->unique();
            $table->string('password', 100);
            $table->string('nik_peserta', 100);
            $table->string('jenis_kelamin', 100);
            $table->string('alamat_peserta', 1000);
            $table->string('pendidikan_peserta', 100);
            $table->timestamps();
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
