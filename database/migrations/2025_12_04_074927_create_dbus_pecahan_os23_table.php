<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('dbus_pecahan_os23', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dbus_id')->constrained('dbuses')->onDelete('cascade'); // Link ke table utama
            $table->string('tahun', 4);
            $table->string('kod_ol')->nullable();   // Contoh: OL23101
            $table->string('sub_kod')->nullable();  // Unik ID untuk input form
            $table->string('butiran')->nullable();  // Contoh: Tenaga Nasional Berhad
            $table->string('no_akaun')->nullable(); // No Akaun Bil
            $table->decimal('anggaran_sebulan', 15, 2)->default(0);
            $table->integer('bil_bulan')->default(12);
            $table->decimal('jumlah', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('dbus_pecahan_os23');
    }
};