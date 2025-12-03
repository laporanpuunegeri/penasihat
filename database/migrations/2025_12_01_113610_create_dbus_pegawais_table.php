<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('dbus_pegawais', function (Blueprint $table) {
            $table->id();
            $table->integer('tahun'); 
            $table->string('nama_pegawai');
            $table->string('gred')->nullable(); 
            
            // 🔥 PASTIKAN KOLUM-KOLUM INI ADA
            $table->decimal('gaji_2025', 15, 2)->default(0); 
            $table->decimal('gaji_2026', 15, 2)->default(0); 
            $table->decimal('jumlah_keseluruhan', 15, 2)->default(0); // Kolum yang hilang tadi
            $table->decimal('kenaikan_peratus', 10, 2)->default(0); 
            $table->decimal('kenaikan_gaji', 10, 2)->default(0); 
            
            $table->string('bulan_pergerakan')->nullable(); 
            $table->text('catatan')->nullable();

            // Kolum Elaun (jika perlu simpan asing)
            $table->decimal('itka', 8, 2)->default(0); 
            $table->decimal('itp', 8, 2)->default(0); 
            $table->decimal('bsh', 8, 2)->default(0); 

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('dbus_pegawais');
    }
};