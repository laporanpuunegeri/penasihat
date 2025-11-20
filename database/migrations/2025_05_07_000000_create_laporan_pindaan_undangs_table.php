<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Jalankan proses migration: cipta jadual 'laporan_pindaan_undangs'
     */
    public function up(): void
    {
        Schema::create('laporan_pindaan_undangs', function (Blueprint $table) {
            $table->id(); // kolum ID auto increment (primary key)
            $table->timestamp('tarikh_daftar')->nullable();
            $table->string('tajuk'); // kolum untuk tajuk laporan
            $table->text('tindakan'); // kolum untuk tindakan yang diambil
            $table->string('status'); // kolum untuk status (cth: Selesai, Dalam Tindakan)
            $table->timestamps(); // cipta 'created_at' dan 'updated_at'
        });
    }

    /**
     * Buang jadual jika migration di-rollback
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_pindaan_undangs');
    }
};
