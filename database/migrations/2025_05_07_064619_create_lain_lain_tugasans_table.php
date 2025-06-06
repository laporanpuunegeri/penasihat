<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lain_lain_tugasans', function (Blueprint $table) {
            $table->id();
            $table->timestamp('tarikh_daftar')->nullable();
            $table->string('perihal');
            $table->date('tarikh');
            $table->enum('tindakan', ['Telah Bincang', 'Telah Hadir']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lain_lain_tugasans');
    }
};
