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
        Schema::create('banner', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id')->nullable()->comment('ID admin yang mengelola banner');
            $table->string('nama_banner');
            $table->string('gambar');
            
            $table->foreign('admin_id')
                  ->references('id')->on('admin')
                  ->onUpdate('cascade')
                  ->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banner');
    }
};
