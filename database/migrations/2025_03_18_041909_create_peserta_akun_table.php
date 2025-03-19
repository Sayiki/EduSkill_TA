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
        Schema::create('peserta_akun', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_peserta');
            $table->string('nama_peserta', 100);
            $table->string('nik_peserta', 16)->unique();
            $table->string('alamat_peserta', 100);
            $table->string('jenis_kelamin', 20);
            $table->string('pendidikan_peserta', 5);
            $table->timestamps();

            // Foreign key ke peserta
            $table->foreign('id_peserta')->references('id')->on('peserta')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peserta_akun');
    }
};
