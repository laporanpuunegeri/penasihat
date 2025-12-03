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
        Schema::table('laporan_pandangan_undangs', function (Blueprint $table) {
            // Tambah column dokumen_path (nullable sebab tak wajib)
            // Pastikan nama column sebelumnya ('status_selesai') wujud, 
            // jika tak pasti boleh buang '->after(...)'
            $table->string('dokumen_path')->nullable()->after('status_selesai'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporan_pandangan_undangs', function (Blueprint $table) {
            $table->dropColumn('dokumen_path');
        });
    }
}; 
