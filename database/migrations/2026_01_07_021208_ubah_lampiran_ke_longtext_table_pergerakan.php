<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pergerakans', function (Blueprint $table) {
            $table->longText('lampiran')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('pergerakans', function (Blueprint $table) {
            $table->string('lampiran')->nullable()->change();
        });
    }
};