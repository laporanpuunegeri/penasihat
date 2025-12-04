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
    Schema::create('dbus_pecahan_os21', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('dbus_id'); // Link ke table induk dbuses
        $table->string('kod_ol')->nullable();      // Cth: OL21101
        $table->string('sub_kod')->nullable();     // Cth: OL21101_G11
        $table->string('butiran')->nullable();     // Cth: Gred 11-13...
        $table->integer('bil_orang')->default(0);
        $table->integer('bil_hari')->default(0);
        $table->decimal('jumlah', 15, 2)->default(0.00); // 15 digit, 2 perpuluhan
        $table->string('catatan')->nullable();
        $table->timestamps(); // Created_at & Updated_at
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dbus_pecahan_os21');
    }
};
