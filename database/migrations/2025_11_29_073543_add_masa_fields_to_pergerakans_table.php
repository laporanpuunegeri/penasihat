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
        $table->string('masa_mula')->nullable();
        $table->string('masa_akhir')->nullable();
    });
}

public function down()
{
    Schema::table('pergerakans', function (Blueprint $table) {
        $table->dropColumn(['masa_mula', 'masa_akhir']);
    });
}
};
