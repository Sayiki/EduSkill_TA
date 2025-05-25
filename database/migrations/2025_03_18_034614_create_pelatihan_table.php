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
        Schema::create('pelatihan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id');
            $table->foreign('admin_id')->references('id')->on('admin')->onDelete('restrict');
            $table->string('nama_pelatihan', 100);
            $table->string('keterangan_pelatihan', 350);
            $table->integer('jumlah_kuota');
            $table->integer('jumlah_peserta');
            $table->dateTime('waktu_pengumpulan');
            $table->timestamps(); //
        });
    }

    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelatihan');
    }
};
