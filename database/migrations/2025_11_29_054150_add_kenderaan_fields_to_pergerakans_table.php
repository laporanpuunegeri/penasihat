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
        // Pastikan nama jadual adalah 'pergerakans'
        Schema::table('pergerakans', function (Blueprint $table) { 
            // Lajur yang diisi oleh Pemohon
            $table->text('tujuan_penggunaan')->nullable();
            $table->text('destinasi')->nullable();
            
            // Lajur yang diisi/diluluskan oleh CC (Pentadbiran)
            $table->string('no_kenderaan')->nullable();
            $table->string('nama_pemandu')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Pastikan nama jadual adalah 'pergerakans'
        Schema::table('pergerakans', function (Blueprint $table) {
            $table->dropColumn(['tujuan_penggunaan', 'destinasi', 'no_kenderaan', 'nama_pemandu']);
        });
    }
};