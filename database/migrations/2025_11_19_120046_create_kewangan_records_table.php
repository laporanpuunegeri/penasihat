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
        // KOD UTAMA: 10000, 20000, 30000...
        $table->string('kod_utama'); 
        // KOD OBJEK: 11000, 21000...
        $table->string('kod_objek'); 
        // BUTIRAN: Gaji, Sewaan...
        $table->string('butiran'); 
        // NILAI DUIT
        $table->decimal('peruntukan', 15, 2)->default(0);
        $table->decimal('belanja', 15, 2)->default(0);
        // Optional: Tahun (kalau nak asingkan tahun depan)
        $table->year('tahun')->default(date('Y'));
        
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
