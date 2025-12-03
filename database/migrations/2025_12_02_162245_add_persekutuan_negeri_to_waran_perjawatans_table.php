<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Logik untuk menambah kolum baharu.
     */
    public function up(): void
    {
        Schema::table('waran_perjawatans', function (Blueprint $table) {
            // Menambah kolum persekutuan (selepas 'bil', nilai lalai 0)
            $table->integer('persekutuan')->default(0)->after('bil');
            
            // Menambah kolum negeri (selepas 'persekutuan', nilai lalai 0)
            $table->integer('negeri')->default(0)->after('persekutuan');
            
            // Kolum 'nota' DIKELUARKAN dari sini kerana ia sudah wujud di DB
            // $table->text('nota')->nullable()->after('kosong'); 

            // Pilihan: Jika anda mahu membuang kolum 'isi' yang lama (kerana ia adalah jumlah persekutuan + negeri)
            // Sila buka komen baris di bawah jika anda ingin membuang kolum 'isi'
            // $table->dropColumn('isi');
        });
    }

    /**
     * Reverse the migrations.
     * Logik untuk membuang semula kolum yang ditambah.
     */
    public function down(): void
    {
        Schema::table('waran_perjawatans', function (Blueprint $table) {
            // Membuang kolum baharu
            $table->dropColumn(['persekutuan', 'negeri', 'nota']);

            // Pilihan: Jika anda membuang 'isi' di atas, anda perlu tambahkannya semula di sini
            // Jika anda membuang 'isi' dalam up(), jangan lupa untuk menambahkannya semula di sini
            // $table->integer('isi')->default(0)->after('bil');
        });
    }
};