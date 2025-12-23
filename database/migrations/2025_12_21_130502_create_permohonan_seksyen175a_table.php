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
    Schema::create('permohonan_seksyen175a', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('agensi_id'); 
        $table->string('no_fail')->nullable(); // Contoh: PTAG. B1/06/06/2020(2024) [cite: 76]
        
        // Maklumat Tanah (Ikut Jadual Borang 10E)
        $table->string('jenis_hakmilik'); // Contoh: E.M.R. [cite: 77]
        $table->string('no_hakmilik');    // Contoh: 1451 [cite: 77]
        $table->string('no_lot');         // Contoh: 1451 [cite: 77]
        $table->string('luas');           // Contoh: 1a. 1r. 12p. [cite: 77]
        $table->string('mukim');          // Contoh: Ayer Paabas [cite: 77]
        $table->string('daerah');         // Contoh: Alor Gajah [cite: 76]
        
        // Sebab Penyediaan
        $table->text('sebab_penyediaan'); // Contoh: Dokumen hakmilik daftar (Buku Daftar) yang asal telah rosak 

        // Status & Admin
        $table->string('status')->default('Baru');
        $table->date('tarikh_notis')->nullable();
        $table->string('nama_pentadbir')->nullable(); // Contoh: BARI'AH BINTI DZULKIFLI [cite: 76]
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permohonan_seksyen175a');
    }
};
