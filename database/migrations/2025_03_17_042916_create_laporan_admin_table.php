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
        Schema::create('laporan_admin', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->foreign('admin_id')
                  ->references('id')->on('admin')
                  ->onUpdate('cascade')
                  ->onDelete('set null');
            $table->timestamp('waktu_upload')->nullable();
            $table->integer('jumlah_peserta')->unsigned();
            $table->integer('jumlah_lulusan_bekerja')->unsigned();
            $table->integer('jumlah_pendaftar')->unsigned();
            $table->string('pelatihan_dibuka', 100);
            $table->string('pelatihan_berjalan', 100);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_admin');
    }
};
