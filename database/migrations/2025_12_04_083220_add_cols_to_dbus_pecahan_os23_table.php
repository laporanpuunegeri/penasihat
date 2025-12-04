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
    Schema::table('dbus_pecahan_os23', function (Blueprint $table) {
        $table->integer('kuantiti')->default(1)->after('no_akaun'); // Untuk Pos & Tel
        $table->string('unit')->nullable()->after('kuantiti');      // Contoh: pack, keping, orang
    });
}

public function down()
{
    Schema::table('dbus_pecahan_os23', function (Blueprint $table) {
        $table->dropColumn(['kuantiti', 'unit']);
    });
}
};
