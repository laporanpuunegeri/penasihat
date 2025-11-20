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
        Schema::create('laporangubalanundangs', function (Blueprint $table) {
            $table->id();

            // ✅ Kolum tambahan
            $table->date('tarikh_daftar')->nullable();

            $table->string('tajuk');       // Tajuk Rang Undang-Undang / Perundangan Subsidiari Substantif
            $table->string('tindakan');    // Tindakan yang diambil
            $table->string('status');      // Status semasa
            $table->timestamps();          // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporangubalanundangs');
    }
};
