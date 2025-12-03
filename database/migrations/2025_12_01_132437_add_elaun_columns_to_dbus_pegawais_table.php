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
        // Tambah kolum elaun yang belum ada
        $table->decimal('itju', 10, 2)->default(0)->after('bsh'); // 12106
        $table->decimal('bipk', 10, 2)->default(0)->after('itju'); // 12107 (Kritikal)
        $table->decimal('bikppk', 10, 2)->default(0)->after('bipk'); // 12108 (Insentif Khas)
        $table->decimal('bitk', 10, 2)->default(0)->after('bikppk'); // 12109 (Tugas Kewangan)
        $table->decimal('biw', 10, 2)->default(0)->after('bitk'); // 12199 (Wilayah)
        $table->decimal('el_lain', 10, 2)->default(0)->after('biw'); // 12199 (Lain-lain)
        
        // Kolum total elaun (untuk simpan jumlah terus)
        $table->decimal('jumlah_elaun_sebulan', 10, 2)->default(0)->after('el_lain');
        $table->decimal('jumlah_elaun_setahun', 15, 2)->default(0)->after('jumlah_elaun_sebulan');
    });
}

public function down()
{
    Schema::table('dbus_pegawais', function (Blueprint $table) {
        $table->dropColumn(['itju', 'bipk', 'bikppk', 'bitk', 'biw', 'el_lain', 'jumlah_elaun_sebulan', 'jumlah_elaun_setahun']);
    });
}
};
