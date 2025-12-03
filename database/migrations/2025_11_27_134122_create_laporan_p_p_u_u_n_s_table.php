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
        // 🔥 CEK DULU: Hanya bina table jika belum wujud
        if (!Schema::hasTable('laporan_p_p_u_u_n_s')) {
            
            Schema::create('laporan_p_p_u_u_n_s', function (Blueprint $table) {
                $table->id();
                $table->string('tahun')->default(date('Y'));

                // Maklumat Laporan
                $table->enum('outcome_id', ['OUTCOME 1', 'OUTCOME 2', 'OUTCOME 3']); 
                $table->string('kpi_desc'); 
                $table->integer('sasaran_tahunan'); 

                // Pencapaian Suku Tahunan
                $table->integer('suku_1')->default(0);
                $table->integer('suku_2')->default(0);
                $table->integer('suku_3')->default(0);
                $table->integer('suku_4')->default(0);

                // Catatan
                $table->json('catatan_data')->nullable(); 

                // Maklumat Pentadbiran
                // Nota: Jika user_id merujuk table 'users', elok guna unsignedBigInteger atau foreignId
                // Tapi jika anda simpan IC/String, kekalkan string.
                $table->string('user_id'); 
                $table->string('status')->default('Draf'); 

                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('laporan_p_p_u_u_n_s');
    }
};