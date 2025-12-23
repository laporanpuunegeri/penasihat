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
    Schema::create('permohonan_seksyen12', function (Blueprint $table) {
        $table->id();
        $table->foreignId('agensi_id')->constrained('agensi_users'); // Hubungkan dengan agensi
        $table->string('jenis_warta')->default('Seksyen 12');
        $table->string('nama_calon_bm');
        $table->string('pelantikan_bm');
        $table->string('pelantikan_bi');
        $table->date('tarikh_lantikan');
        $table->date('tarikh_tt');
        $table->string('no_fail');
        $table->string('status')->default('Belum Disemak');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permohonan_seksyen12s');
    }
};
