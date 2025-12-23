<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('permohonan_seksyen62', function (Blueprint $table) {
        $table->id();
        $table->foreignId('agensi_id')->constrained('agensi_users'); 
        
        // Data Dwibahasa (Kena isi dua kotak)
        $table->text('tujuan_bm');
        $table->text('tujuan_bi');
        $table->string('kawalan_bm');
        $table->string('kawalan_bi');
        $table->string('selenggara_bm');
        $table->string('selenggara_bi');
        
        // Data Fakta (Satu kotak)
        $table->string('daerah');
        $table->string('mukim');
        $table->string('no_lot');
        $table->string('no_pa');
        $table->string('luas');
        $table->text('no_fail');
        $table->date('tarikh_tt');
        
        $table->string('status')->default('Belum Disemak');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permohonan_seksyen62s');
    }
};
