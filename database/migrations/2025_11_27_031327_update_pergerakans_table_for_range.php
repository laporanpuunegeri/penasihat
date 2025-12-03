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
        Schema::table('pergerakans', function (Blueprint $table) {
            
            // 1. Cek Tarikh Mula
            if (!Schema::hasColumn('pergerakans', 'tarikh_mula')) {
                $table->date('tarikh_mula')->nullable();
            }

            // 2. Cek Tarikh Akhir
            if (!Schema::hasColumn('pergerakans', 'tarikh_akhir')) {
                $table->date('tarikh_akhir')->nullable();
            }

            // 3. Cek Kenderaan
            if (!Schema::hasColumn('pergerakans', 'kenderaan')) {
                $table->string('kenderaan')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('pergerakans', function (Blueprint $table) {
            // Pilihan: Boleh drop column jika rollback (hati-hati, data akan hilang)
            // $table->dropColumn(['tarikh_mula', 'tarikh_akhir', 'kenderaan']);
        });
    }
};