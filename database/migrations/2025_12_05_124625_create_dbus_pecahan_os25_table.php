<?php

// database/migrations/..._create_dbus_pecahan_os25_table.php (di dalam function up())

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dbus_pecahan_os25', function (Blueprint $table) {
            $table->id();
            // Kolum utama (Master Reference)
            $table->foreignId('dbus_id')->constrained('dbuses'); // Foreign key ke rekod induk DBUS (OS25000)
            
            // Kolum Identifikasi Pecahan
            $table->string('kod_ol', 10);
            $table->string('kod_pecahan_sub', 30)->unique(); // Cth: OL25499_1
            $table->year('tahun');
            
            // Kolum Data
            $table->string('butiran')->nullable();
            $table->decimal('kuantiti', 8, 2)->default(0);
            $table->integer('bil_bulan')->default(0);
            $table->decimal('anggaran_sebulan', 10, 2)->default(0);
            $table->decimal('jumlah', 10, 2)->default(0);
            $table->string('catatan', 255)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dbus_pecahan_os25');
    }
};