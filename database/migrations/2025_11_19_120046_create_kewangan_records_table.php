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
        Schema::create('kewangan_records', function (Blueprint $table) {
            $table->id();

            // 1. WAJIB ADA: Negeri (Sebab controller awak check 'where negeri')
            $table->string('negeri')->index(); 

            // 2. Kod & Butiran
            $table->string('kod_utama'); // 10000
            $table->string('kod_objek'); // 11000
            $table->string('butiran');   // Gaji & Upahan

            // 3. Duit (Decimal 15 digit, 2 titik perpuluhan)
            $table->decimal('peruntukan', 15, 2)->default(0);
            
            // 4. Belanja Suku Tahun (INI YANG TERTINGGAL SEBELUM NI)
            $table->decimal('belanja_s1', 15, 2)->default(0);
            $table->decimal('belanja_s2', 15, 2)->default(0);
            $table->decimal('belanja_s3', 15, 2)->default(0);
            $table->decimal('belanja_s4', 15, 2)->default(0);

            // 5. Jumlah Besar Belanja
            $table->decimal('belanja', 15, 2)->default(0);

            // 6. Tahun (Integer lebih selamat dari Year untuk elak isu format)
            $table->integer('tahun')->default(date('Y'));
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kewangan_records');
    }
};