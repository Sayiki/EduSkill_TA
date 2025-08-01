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
        Schema::create('pelatihan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->unsignedBigInteger('mentor_id')->nullable(); 
            $table->string('nama_pelatihan', 100);
            $table->string('foto_pelatihan')->nullable();
            $table->unsignedBigInteger('kategori_id')->nullable(); 
            $table->integer('biaya');
            $table->string('keterangan_pelatihan', 350);
            $table->integer('jumlah_kuota');
            $table->integer('jumlah_peserta')->default(0);
            $table->dateTime('waktu_pengumpulan');
            $table->enum('status_pelatihan', ['Belum Dimulai', 'Sedang berlangsung', 'Selesai'])->default('Belum Dimulai');
            $table->enum('post_status', ['Archived', 'Published'])->default('Archived');
            $table->timestamps();

            $table->foreign('admin_id')
                  ->references('id')->on('admin')
                  ->onUpdate('cascade')
                  ->onDelete('set null');
        
            $table->foreign('mentor_id')
                  ->references('id')->on('mentor')
                  ->onUpdate('cascade')
                  ->onDelete('set null');
            $table->foreign('kategori_id')
                  ->references('id')->on('kategori_pelatihan') 
                  ->onUpdate('cascade')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pelatihan', function (Blueprint $table) {
            // Urutan drop foreign key bisa penting, coba drop mentor_id dulu jika ada dependensi
            if (Schema::hasColumn('pelatihan', 'mentor_id')) {
                try {
                    $table->dropForeign(['mentor_id']);
                } catch (\Exception $e) {
                    // Abaikan jika constraint tidak ditemukan atau sudah di-drop
                }
            }
            if (Schema::hasColumn('pelatihan', 'admin_id')) {
                try {
                    $table->dropForeign(['admin_id']);
                } catch (\Exception $e) {
                    // Abaikan jika constraint tidak ditemukan atau sudah di-drop
                }
            }
        });
        Schema::dropIfExists('pelatihan');
    }
};
