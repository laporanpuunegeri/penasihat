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
        Schema::create('permohonan_seksyen64s', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agensi_id'); // Rujukan kepada agensi yang memohon
            
            // --- MAKLUMAT WARTA ASAL (RUJUKAN PEMBATALAN) ---
            $table->string('no_warta_asal'); // Contoh: 496 
            $table->date('tarikh_warta_asal'); // Contoh: 21 Disember 2017 
            
            // --- MAKLUMAT TANAH (JADUAL) ---
            $table->string('daerah'); // Contoh: Jasin 
            $table->string('mukim'); // Contoh: Kesang 
            $table->string('no_lot'); // Contoh: 6799 
            $table->string('no_pa'); // Contoh: P.A. 100018 
            $table->decimal('luas', 12, 2); // Contoh: 4,176.00 
            
            // --- TUJUAN RIZAB ASAL (DWIBAHASA) ---
            $table->text('tujuan_bm'); // Contoh: Tapak Tanah Perkuburan Islam [cite: 16]
            $table->text('tujuan_bi'); // Contoh: Islamic Cemetery Site [cite: 21]
            $table->string('kawalan_bm'); // Contoh: Pengarah Jabatan Agama Islam Melaka [cite: 16]
            $table->string('kawalan_bi'); // Contoh: Director of the Melaka Islamic Religious Department [cite: 21]

            // --- PENGESAHAN ---
            $table->string('no_fail'); // Contoh: PTG(M)R/370/... [cite: 26]
            $table->date('tarikh_tt'); // Tarikh tandatangan warta baru [cite: 27]
            $table->string('status')->default('Belum Disemak'); // Status permohonan

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permohonan_seksyen64s');
    }
};