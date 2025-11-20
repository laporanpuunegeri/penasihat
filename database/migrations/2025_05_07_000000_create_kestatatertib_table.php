<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ✅ Pastikan nama jadual adalah betul: 'kestatatertib'
        Schema::create('kestatatertib', function (Blueprint $table) {
            $table->id();
            $table->timestamp('tarikh_daftar')->nullable();
            $table->date('tarikh_terima')->nullable();
            $table->string('kategori')->nullable();
            $table->text('fakta_ringkasan')->nullable();
            $table->text('isu')->nullable();
            $table->text('ringkasan_pandangan')->nullable();
            $table->string('status')->nullable(); // ✅ status string
            $table->date('tarikh_selesai')->nullable();
            $table->boolean('hantar_kepada_boss')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kestatatertib');
    }
};
