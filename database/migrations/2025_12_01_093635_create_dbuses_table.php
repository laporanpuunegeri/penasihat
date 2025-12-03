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
    Schema::create('dbuses', function (Blueprint $table) {
        $table->id();
        $table->string('kod_objek'); // Contoh: OA10000, OS11000, OL11101
        $table->string('perkara');   // Contoh: EMOLUMEN, GAJI DAN UPAHAN
        $table->string('jenis');     // 'OA' (Am), 'OS' (Sebagai), 'OL' (Lanjut)
        $table->decimal('jumlah', 15, 2)->default(0); // Nilai duit
        $table->integer('tahun');    // Tahun Anggaran (cth: 2026)
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dbuses');
    }
};
