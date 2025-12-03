<?php

// database/migrations/...create_dbus_pecahan_os15_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dbus_pecahan_os15', function (Blueprint $table) {
            $table->id();
            $table->integer('tahun');
            $table->string('kod_ol', 10); // Contoh: OL15101, OL15110
            $table->string('sub_kod', 10)->nullable(); // Contoh: 1.1, 2.1, 7.1a
            $table->string('butiran', 255)->nullable(); // Contoh: Nama Pegawai, Yuran Tahunan Badan Profesional
            
            $table->decimal('anggaran', 10, 2)->default(0); // Nilai RM Anggaran
            $table->integer('bil_unit')->default(0); // Bilangan unit (orang, bulan, dsb.)
            $table->decimal('jumlah_pecahan', 12, 2)->default(0); // Jumlah per baris
            $table->string('catatan')->nullable();
            $table->timestamps();
            
            // Index unik untuk carian pantas mengikut tahun dan sub-kod
            $table->unique(['tahun', 'kod_ol', 'sub_kod']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dbus_pecahan_os15');
    }
};