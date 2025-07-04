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
            // Definisikan semua kolom terlebih dahulu
            $table->id();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->unsignedBigInteger('mentor_id')->nullable(); // Didefinisikan setelah admin_id, tanpa ->after()
            $table->string('nama_pelatihan', 100);
            $table->string('kategori', 25);
            $table->integer('biaya', 100);
            $table->string('keterangan_pelatihan', 350);
            $table->integer('jumlah_kuota');
            $table->integer('jumlah_peserta')->default(0);
            $table->dateTime('waktu_pengumpulan');
            $table->enum('status_pelatihan', ['Dimulai', 'Sedang berlangsung', 'Selesai'])->default('Dimulai');
            $table->enum('post_status', ['Draft', 'Published'])->default('Draft');
            $table->timestamps();

            // Kemudian definisikan semua foreign key constraints
            $table->foreign('admin_id')
                  ->references('id')->on('admin')
                  ->onUpdate('cascade')
                  ->onDelete('set null');
            
            $table->foreign('mentor_id')
                  ->references('id')->on('mentor')
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
