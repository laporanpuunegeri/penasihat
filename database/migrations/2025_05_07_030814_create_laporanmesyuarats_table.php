<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_mesyuarats', function (Blueprint $table) {
            $table->id();
            $table->timestamp('tarikh_daftar')->nullable();
            $table->string('mesyuarat');
            $table->text('isu');
            $table->date('tarikh_mesyuarat');
            $table->string('status');
            $table->string('pandangan'); // nilai: 'Lisan' atau 'Bertulis'
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_mesyuarats');
    }
};
