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
            $table->string('nik_peserta', 100);
            $table->string('jenis_kelamin', 100);
            $table->string('alamat_peserta', 1000);
            $table->unsignedBigInteger('id_pendidikan')->nullable();
            $table->timestamps();
        
            $table->foreign('id_pendidikan')->references('id')->on('pendidikan')->onDelete('set null');
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
