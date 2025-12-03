<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('dbus_pegawais', function (Blueprint $table) {
            // Cek jika kolum TIADA, baru tambah
            if (!Schema::hasColumn('dbus_pegawais', 'el_keraian')) {
                // Letak selepas ITP supaya urutan cantik
                $table->decimal('el_keraian', 10, 2)->default(0)->after('itp'); 
            }
        });
    }

    public function down()
    {
        Schema::table('dbus_pegawais', function (Blueprint $table) {
            if (Schema::hasColumn('dbus_pegawais', 'el_keraian')) {
                $table->dropColumn('el_keraian');
            }
        });
    }
};