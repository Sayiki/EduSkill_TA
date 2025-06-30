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
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peserta_id')->constrained('peserta')->onDelete('cascade');
            $table->foreignId('daftar_pelatihan_id')->constrained('daftar_pelatihan')->onDelete('cascade');
            $table->text('comment');
            $table->string('tempat_kerja')->nullable();
            $table->string('status')->default('Ditinjau');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};
