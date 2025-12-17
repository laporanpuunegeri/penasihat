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
        Schema::create('lampiran_kes_mahkamah', function (Blueprint $table) {
            $table->id();
            
            // Column Asas
            $table->integer('user_id')->nullable();
            $table->integer('bulan')->nullable();
            $table->integer('tahun')->nullable();
            $table->string('kategori')->nullable();
            $table->integer('bil_aktif')->default(0);
            $table->integer('majistret')->default(0);
            $table->integer('sesi')->default(0);
            $table->integer('tinggi')->default(0);
            $table->integer('rayuan')->default(0);
            $table->integer('persk')->default(0);
            $table->string('negeri')->nullable();
            
            // Column Status
            $table->text('status')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lampiran_kes_mahkamah');
    }
};