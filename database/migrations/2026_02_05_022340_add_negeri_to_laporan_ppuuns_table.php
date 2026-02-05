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
    Schema::table('laporan_p_p_u_u_n_s', function (Blueprint $table) { 
        $table->string('negeri')->nullable()->after('id')->index();
    });
}

public function down()
{
    Schema::table('laporan_p_p_u_u_n_s', function (Blueprint $table) {
        $table->dropColumn('negeri');
    });
}
};
