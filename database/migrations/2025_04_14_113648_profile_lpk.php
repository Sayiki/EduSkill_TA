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
        Schema::create('profile_lpk', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lembaga_id');
            $table->string('nama_lpk');
            $table->text('deskripsi_lpk');
            $table->string('foto_lpk')->nullable();
            $table->timestamps();
        
            $table->foreign('lembaga_id')->references('id')->on('informasi_lembaga')->onDelete('cascade');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_lkp');
    }
};
