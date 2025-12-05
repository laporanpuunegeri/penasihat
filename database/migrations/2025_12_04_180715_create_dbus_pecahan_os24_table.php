<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('dbus_pecahan_os24', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dbus_id')->constrained('dbuses')->onDelete('cascade'); // Link ke table utama
            $table->string('tahun', 4);
            $table->string('kod_ol');   // Contoh: OL24101
            $table->string('sub_kod');  // Unik ID untuk input form (cth: OL24101_MESIN_CETAK)
            $table->string('butiran')->nullable();  // Contoh: Sewa Mesin Fotostat Toshiba
            $table->decimal('anggaran_sebulan', 15, 2)->default(0);
            $table->integer('bil_bulan')->default(12);
            $table->decimal('jumlah', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('dbus_pecahan_os24');
    }
};