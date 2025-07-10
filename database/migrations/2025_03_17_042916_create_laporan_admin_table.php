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
            // The primary key for the table.
            $table->id();

            // The foreign key to link to the 'admin' table.
            $table->unsignedBigInteger('admin_id')->nullable();

            // The description of the report. Using TEXT allows for longer descriptions.
            $table->text('laporan_deskripsi');

            $table->string('laporan_file')->nullable();

            // The standard 'created_at' and 'updated_at' timestamp columns.
            $table->timestamps();

            // Defines the foreign key constraint.
            $table->foreign('admin_id')
                  ->references('id')->on('admin')
                  ->onDelete('set null'); // If the admin is deleted, this ID becomes null.
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
