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
    Schema::table('laporan_pandangan_undangs', function (Blueprint $table) {
        $table->boolean('is_current')->default(true)->after('id'); // Default true (aktif)
        $table->unsignedBigInteger('previous_id')->nullable()->after('id'); // ID rekod sebelum
        
        // Index untuk lajukan query nanti
        $table->index('is_current');
        $table->index('previous_id');
    });
}

public function down()
{
    Schema::table('laporan_pandangan_undangs', function (Blueprint $table) {
        $table->dropColumn(['is_current', 'previous_id']);
    });
}
};
