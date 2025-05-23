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
        Schema::create('informasi_lembaga', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')
            ->constrained('admin')
            ->onDelete('cascade');
            $table->text('visi');
            $table->text('misi');
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('informasi_lembaga');
    }
};
