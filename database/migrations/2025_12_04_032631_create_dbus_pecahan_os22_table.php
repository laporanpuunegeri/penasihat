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
    Schema::create('dbus_pecahan_os22', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('dbus_id'); // Link ke table induk
        $table->string('kod_ol')->nullable();      // Cth: OL22155
        $table->string('sub_kod')->nullable();     // Cth: OL22155_PINDAH
        $table->string('butiran')->nullable();     // Cth: Pengangkutan Barang...
        $table->decimal('anggaran', 15, 2)->default(0.00); // Kos Seunit/Sekali
        $table->integer('bil_unit')->default(0);           // Kekerapan/Bilangan
        $table->decimal('jumlah', 15, 2)->default(0.00);   // Total
        $table->string('catatan')->nullable();
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('dbus_pecahan_os22');
}
};
