<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_kes_mahkamah', function (Blueprint $table) {
            $table->id();

            // Tambah tarikh daftar secara manual (jika digunakan secara berasingan)
            $table->timestamp('tarikh_daftar')->nullable();

            // Maklumat laporan
            $table->string('jenis_kes');
            $table->date('tarikh_sebutan');
            $table->text('fakta_ringkas');
            $table->text('isu');
            $table->string('skop_tugas')->nullable();
            $table->text('ringkasan_hujahan');
            $table->string('status');
        
            // Tambahan lain
            $table->boolean('hantar_kepada_boss')->nullable()->default(false);

            // ✅ Ini WAJIB untuk guna created_at & updated_at secara automatik
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_kes_mahkamah');
    }
};
