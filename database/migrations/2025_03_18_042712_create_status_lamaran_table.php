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
        Schema::create('status_lamaran', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_peserta');
            $table->string('nama_pelatihan', 100);
            $table->string('status', 10);
            $table->timestamps();

            // Foreign key
            $table->foreign('id_peserta')->references('id')->on('peserta')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status_lamaran');
    }
};
