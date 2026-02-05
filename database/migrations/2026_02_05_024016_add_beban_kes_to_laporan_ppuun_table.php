<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up()
{

    Schema::table('laporan_p_p_u_u_n_s', function (Blueprint $table) {

        $table->json('beban_kes')->nullable()->after('catatan_data');
    });
}

public function down()
{
    Schema::table('laporan_p_p_u_u_n_s', function (Blueprint $table) {
        $table->dropColumn('beban_kes');
    });
}
};
