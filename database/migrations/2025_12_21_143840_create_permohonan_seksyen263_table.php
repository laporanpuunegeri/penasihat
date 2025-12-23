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
        Schema::create('permohonan_seksyen263', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agensi_id');
            $table->string('no_fail')->nullable();
            
            // Maklumat Lelongan & Kewangan
            $table->string('nama_pentadbir_tanah');
            $table->string('ic_pentadbir');
            $table->date('tarikh_lelongan');
            $table->string('hari_lelongan');
            $table->time('masa_lelongan');
            $table->string('tempat_lelongan');
            $table->decimal('harga_rizab', 15, 2);
            $table->decimal('deposit_sepuluh_peratus', 15, 2);
            $table->decimal('amaun_hutang', 15, 2);
            $table->string('nama_pemegang_gadai');
            $table->date('tarikh_akhir_bayaran')->nullable();

            // Maklumat Tanah (Jadual)
            $table->string('mukim');
            $table->string('no_lot');
            $table->string('jenis_hakmilik');
            $table->string('no_hakmilik');
            $table->string('bahagian_tanah')->default('1/1 bhgn');
            $table->string('no_daftar_gadaian');
            
            $table->string('status')->default('Baru');
            $table->date('tarikh_perintah');
            $table->timestamps();
        });
    } // Pastikan ada penutup kurungan berangkai ini di sini!

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permohonan_seksyen263');
    }
};