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
            $table->foreignId('admin_id')
            ->constrained('admin')
            ->onDelete('cascade');
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
