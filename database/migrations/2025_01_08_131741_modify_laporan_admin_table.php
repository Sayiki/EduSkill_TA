<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations to modify the table.
     */
    public function up(): void
    {
        Schema::table('laporan_admin', function (Blueprint $table) {
            // Define the columns to be dropped
            $columnsToDrop = [
                'waktu_upload',
                'jumlah_peserta',
                'jumlah_lulusan_bekerja',
                'jumlah_pendaftar',
                'pelatihan_dibuka',
                'pelatihan_berjalan',
            ];

            // Drop the unnecessary columns if they exist
            $table->dropColumn($columnsToDrop);

            // Add the new 'laporan_deskripsi' column if it doesn't exist
            if (!Schema::hasColumn('laporan_admin', 'laporan_deskripsi')) {
                $table->text('laporan_deskripsi')->after('admin_id');
            }
        });
    }

    /**
     * Reverse the migrations to restore the original schema.
     */
    public function down(): void
    {
        Schema::table('laporan_admin', function (Blueprint $table) {
            // Drop the new column if it exists
            if (Schema::hasColumn('laporan_admin', 'laporan_deskripsi')) {
                $table->dropColumn('laporan_deskripsi');
            }

            // Re-add the old columns to make the migration reversible
            $table->timestamp('waktu_upload')->nullable();
            $table->integer('jumlah_peserta')->unsigned();
            $table->integer('jumlah_lulusan_bekerja')->unsigned();
            $table->integer('jumlah_pendaftar')->unsigned();
            $table->string('pelatihan_dibuka', 100);
            $table->string('pelatihan_berjalan', 100);
        });
    }
};
