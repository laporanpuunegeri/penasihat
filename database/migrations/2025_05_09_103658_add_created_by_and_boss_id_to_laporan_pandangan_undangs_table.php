<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('laporan_pandangan_undangs', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->nullable()->after('id');
            $table->unsignedBigInteger('boss_id')->nullable()->after('created_by');
        });
    }

    public function down(): void
    {
        Schema::table('laporan_pandangan_undangs', function (Blueprint $table) {
            $table->dropColumn(['created_by', 'boss_id']);
        });
    }
};
