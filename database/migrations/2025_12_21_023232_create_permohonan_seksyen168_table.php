<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permohonan_seksyen168', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agensi_id'); //
            $table->string('no_fail')->nullable();
            
            // Maklumat Pemilik
            $table->string('nama_pemilik');
            $table->string('no_kp_pemilik')->nullable();
            $table->text('alamat_pemilik')->nullable();
            
            // Maklumat Tanah
            $table->string('jenis_hakmilik');
            $table->string('no_hakmilik');
            $table->string('no_lot');
            $table->string('mukim');
            $table->string('daerah');
            $table->string('luas')->nullable(); // Optional jika perlu
            
            // Tambahan untuk Seksyen 168 (Sebab Hilang/Rosak)
            $table->text('sebab_permohonan')->nullable(); 

            // Status & Admin
            $table->string('status')->default('Baru');
            $table->date('tarikh_notis')->nullable();
            $table->string('nama_pentadbir')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permohonan_seksyen168');
    }
};