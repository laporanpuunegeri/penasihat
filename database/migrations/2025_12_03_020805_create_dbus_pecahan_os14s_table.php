<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dbus_pecahan_os14', function (Blueprint $table) {
            $table->id();
            $table->integer('tahun');
            $table->string('gred', 50);
            $table->decimal('anggaran', 10, 2)->default(0); 
            $table->integer('bil_orang')->default(0);
            $table->integer('bil_bulan')->default(0);
            $table->decimal('jumlah_pecahan', 12, 2)->default(0);
            $table->string('catatan')->nullable();
            $table->timestamps();
            
            $table->unique(['tahun', 'gred']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dbus_pecahan_os14');
    }
};