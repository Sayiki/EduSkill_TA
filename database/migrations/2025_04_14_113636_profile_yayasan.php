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
        Schema::create('profile_yayasan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')
            ->constrained('admin')
            ->onDelete('cascade');
            $table->text('deskripsi_yayasan');
            $table->string('nama_yayasan');
            $table->string('foto_yayasan')->nullable();
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_yayasan');
    }
};
