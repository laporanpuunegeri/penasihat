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
        Schema::create('dbus_pecahan_os27', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dbus_id')->constrained('dbuses');
            $table->string('kod_ol', 10);
            $table->string('kod_pecahan_sub', 30)->unique();
            $table->year('tahun');
            $table->string('butiran')->nullable();
            $table->decimal('kuantiti', 8, 2)->default(0);
            $table->integer('bil_bulan')->default(0);
            $table->decimal('anggaran_sebulan', 10, 2)->default(0);
            $table->decimal('jumlah', 10, 2)->default(0);
            $table->string('catatan', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dbus_pecahan_os27');
    }
};
