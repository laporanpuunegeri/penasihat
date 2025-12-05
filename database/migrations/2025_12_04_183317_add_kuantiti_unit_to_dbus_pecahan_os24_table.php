<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('dbus_pecahan_os24', function (Blueprint $table) {
            $table->integer('kuantiti')->default(1)->after('butiran');
            $table->string('unit')->nullable()->after('kuantiti');
        });
    }

    public function down()
    {
        Schema::table('dbus_pecahan_os24', function (Blueprint $table) {
            $table->dropColumn(['kuantiti', 'unit']);
        });
    }
};