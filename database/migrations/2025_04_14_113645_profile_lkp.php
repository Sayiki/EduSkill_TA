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
        Schema::create('profile_lkp', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_lembaga');
            $table->string('nama_lkp');
            $table->text('deskripsi_lkp');
            $table->string('foto_lkp')->nullable();
            $table->timestamps();

            $table->foreign('id_lembaga')->references('id')->on('informasi_lembaga')->onDelete('cascade');
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
