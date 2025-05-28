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
        Schema::create('berita', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->string('title');
            $table->text('deskripsi');
            $table->string('gambar')->nullable(); // Path ke gambar berita
            $table->date('date'); // Tanggal publikasi berita
            
            $table->foreign('admin_id')
                  ->references('id')->on('admin')
                  ->onUpdate('cascade')
                  ->onDelete('set null');
                  
            $table->timestamps(); // Kolom created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('berita');
    }
};
