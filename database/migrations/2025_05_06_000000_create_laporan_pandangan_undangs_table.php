<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('laporan_pandangan_undangs', function (Blueprint $table) {
            $table->id();

            // Kolum tambahan diminta
            $table->date('tarikh_daftar')->nullable(); // <== DITAMBAH DI SINI
            $table->string('kategori');
            $table->string('isu');
            $table->date('tarikh_terima');
            $table->text('fakta_ringkasan');
            $table->text('isu_detail');
            $table->text('ringkasan_pandangan');

            // Ubah kepada JSON untuk selaras dengan model casts['jenis_pandangan' => 'array']
            $table->string('jenis_pandangan')->nullable();

            $table->text('status');
            $table->date('tarikh_selesai')->nullable();

            // Elak nullable untuk boolean dan tetapkan default (amalan terbaik)
            $table->boolean('belum_selesai')->default(false);
            $table->boolean('dirujuk_jpn')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_pandangan_undangs');
    }
};
