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
    Schema::create('permohonan_seksyen261', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('agensi_id'); // Untuk tahu agensi mana yang daftar
        $table->string('no_fail')->nullable(); // [cite: 27]
        
        // Maklumat Penggadai & Siasatan
        $table->string('nama_penggadai'); // [cite: 22]
        $table->text('alamat_penggadai'); // [cite: 22, 23]
        $table->string('nama_pemegang_gadai'); // [cite: 24]
        $table->string('tempat_siasatan')->default('Bilik Perbicaraan Pejabat Tanah'); // [cite: 25]
        $table->date('tarikh_siasatan'); // [cite: 25]
        $table->time('masa_siasatan'); // [cite: 25]
        
        // Maklumat Tanah (Ikut Jadual Melintang)
        $table->string('mukim'); // [cite: 28]
        $table->string('no_lot'); // [cite: 28]
        $table->string('jenis_hakmilik'); // [cite: 28]
        $table->string('no_hakmilik'); // [cite: 28]
        $table->string('bahagian_tanah')->default('1/1 bhgn.'); // [cite: 28]
        $table->string('no_daftar_gadaian'); // [cite: 28]

        // Pentadbiran
        $table->string('status')->default('Baru');
        $table->date('tarikh_notis')->nullable(); // Tarikh Saman dikeluarkan
        $table->string('nama_pentadbir')->nullable(); // [cite: 27]
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permohonan_seksyen261');
    }
};
