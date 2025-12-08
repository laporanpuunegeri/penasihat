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
        $table->string('lampiran')->nullable(); // Simpan path fail
    });
}

public function down()
{
    Schema::table('pergerakans', function (Blueprint $table) {
        $table->dropColumn('lampiran');
    });
}
};
