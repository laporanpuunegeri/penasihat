<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_semakan_undang', function (Blueprint $table) {
            $table->id();
            $table->timestamp('tarikh_daftar')->nullable();
            $table->string('tajuk', 500);
            $table->text('tindakan');
            $table->string('status', 100);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_semakan_undang');
    }
};
