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
        Schema::create('permohonan_seksyen130', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agensi_id'); // Rujuk agensi login
            $table->string('no_fail')->nullable();
            $table->string('nama_pemilik');
            $table->string('no_kp_pemilik')->nullable();
            $table->text('alamat_pemilik')->nullable();
            
            // Maklumat Tanah
            $table->string('jenis_hakmilik'); // P.N., G.M., dll
            $table->string('no_hakmilik');
            $table->string('no_lot');
            $table->string('luas'); // String sebab ada unit 'Meter Persegi'
            $table->string('mukim');
            $table->string('daerah');
            
            // Status & Admin
            $table->string('status')->default('Baru'); // Supaya tak kosong di index
            $table->date('tarikh_notis')->nullable();
            $table->string('nama_pentadbir')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Pastikan nama table SAMA dengan di atas (tiada 's' di hujung)
        Schema::dropIfExists('permohonan_seksyen130');
    }
};