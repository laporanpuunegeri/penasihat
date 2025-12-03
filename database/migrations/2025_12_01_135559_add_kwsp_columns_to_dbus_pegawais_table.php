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
    Schema::table('dbus_pegawais', function (Blueprint $table) {
        // Fasa 1: Gaji Sekarang / Semasa
        $table->decimal('kwsp_gaji_semasa', 10, 2)->default(0)->after('catatan');
        $table->integer('kwsp_peratus_semasa')->default(0)->after('kwsp_gaji_semasa');
        $table->integer('kwsp_bulan_semasa')->default(0)->after('kwsp_peratus_semasa');

        // Fasa 2: Gaji Selepas Pelarasan
        $table->decimal('kwsp_gaji_baru', 10, 2)->default(0)->after('kwsp_bulan_semasa');
        $table->integer('kwsp_peratus_baru')->default(0)->after('kwsp_gaji_baru');
        $table->integer('kwsp_bulan_baru')->default(0)->after('kwsp_peratus_baru');

        // Total
        $table->decimal('kwsp_total', 15, 2)->default(0)->after('kwsp_bulan_baru');
    });
}

public function down()
{
    Schema::table('dbus_pegawais', function (Blueprint $table) {
        $table->dropColumn([
            'kwsp_gaji_semasa', 'kwsp_peratus_semasa', 'kwsp_bulan_semasa',
            'kwsp_gaji_baru', 'kwsp_peratus_baru', 'kwsp_bulan_baru',
            'kwsp_total'
        ]);
    });
}
};
