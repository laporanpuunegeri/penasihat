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
    Schema::create('dbus_pecahan_os28', function (Blueprint $table) {
        $table->id();
        $table->foreignId('dbus_id')->constrained('dbuses')->onDelete('cascade');
        $table->string('kod_ol'); // Contoh: OL28102
        $table->string('kod_pecahan_sub'); // ID Unik Item
        $table->year('tahun');
        $table->string('butiran')->nullable();
        $table->decimal('kuantiti', 8, 2)->default(0);
        $table->decimal('bil_servis', 8, 2)->default(0); // Guna 'bil_servis' ikut PDF
        $table->decimal('anggaran_kos', 10, 2)->default(0);
        $table->decimal('jumlah', 15, 2)->default(0);
        $table->string('catatan')->nullable();
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('dbus_pecahan_os28');
}
};
