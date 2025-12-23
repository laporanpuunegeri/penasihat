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
    Schema::create('permohonan_seksyen175d', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('agensi_id'); 
        $table->string('no_fail')->nullable(); // [cite: 75, 79]
        
        // Maklumat Tanah (Ikut Jadual Borang 10H)
        $table->string('daerah');         // [cite: 76, 80]
        $table->string('mukim');          // [cite: 76, 80]
        $table->string('jenis_hakmilik'); // [cite: 76, 80]
        $table->string('no_hakmilik');    // [cite: 76, 80]
        $table->string('no_lot');         // [cite: 76, 80]
        $table->string('luas');           // 
        
        // Maklumat Pemilik & Kepentingan
        $table->string('nama_pemilik');   // 
        $table->string('no_kp_pemilik')->nullable(); // 
        $table->string('bahagian_tanah')->default('1/1'); // 

        // Status & Admin
        $table->string('status')->default('Baru');
        $table->date('tarikh_notis')->nullable();
        $table->string('nama_pentadbir')->nullable(); // [cite: 76, 80]
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permohonan_seksyen175d');
    }
};
