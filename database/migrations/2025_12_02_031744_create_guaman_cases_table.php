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
        Schema::create('guaman_cases', function (Blueprint $table) {
            $table->id();
            
            // RUJUKAN & KOD KES (DARI DOKUMEN WORD)
            $table->string('kod_perkara', 50)->nullable(); // Cth: KOD 01, KOD 02.
            $table->string('rujukan_fail')->nullable(); // Cth: PN/MK/PP/01/02/01/2023 (2022)
            $table->string('kategori_kes')->nullable(); // Cth: Kewarganegaraan, Tatatertib, Perlembagaan
            $table->string('kendalian_oleh')->nullable(); // Cth: PGN, FC Izzatul, FC Maryam
            
            // MAHKAMAH
            $table->string('mahkamah')->nullable(); // Cth: Mahkamah Rayuan Malaysia, Mahkamah Tinggi Muar
            $table->string('rujukan_mahkamah')->nullable(); // Cth: Rayuan Sivil No: M-01(A)-206-04/2023
            
            // PIHAK BERLAWANAN & STATUS
            // Menyimpan senarai pihak yang terlibat (Plaintif V. Defendan)
            $table->text('pihak_berlawanan')->nullable(); 
            $table->string('status_kes')->default('Kendalian PGN');
            
            // TARIKH
            $table->date('tarikh_buka')->nullable(); // Digunakan untuk 'orderBy'
            
            // PENGGUNA
            $table->string('user_id')->nullable(); // ID Staf yang mendaftar

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guaman_cases');
    }
};