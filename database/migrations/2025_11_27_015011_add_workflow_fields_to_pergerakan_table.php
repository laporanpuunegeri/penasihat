<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // status_cc
        if (!Schema::hasColumn('pergerakans', 'status_cc')) {
            Schema::table('pergerakans', function (Blueprint $table) {
                $table->enum('status_cc', ['Pending', 'Sokong', 'Tolak'])->default('Pending');
            });
        }

        // status_yb
        if (!Schema::hasColumn('pergerakans', 'status_yb')) {
            Schema::table('pergerakans', function (Blueprint $table) {
                $table->enum('status_yb', ['Pending', 'Lulus', 'Tolak'])->default('Pending');
            });
        }

        // catatan_cc
        if (!Schema::hasColumn('pergerakans', 'catatan_cc')) {
            Schema::table('pergerakans', function (Blueprint $table) {
                $table->text('catatan_cc')->nullable();
            });
        }

        // catatan_yb
        if (!Schema::hasColumn('pergerakans', 'catatan_yb')) {
            Schema::table('pergerakans', function (Blueprint $table) {
                $table->text('catatan_yb')->nullable();
            });
        }
    }

    public function down()
    {
        Schema::table('pergerakans', function (Blueprint $table) {
            foreach (['status_cc', 'status_yb', 'catatan_cc', 'catatan_yb'] as $col) {
                if (Schema::hasColumn('pergerakans', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
