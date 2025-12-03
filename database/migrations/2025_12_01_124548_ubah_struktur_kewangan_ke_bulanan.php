<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kewangan_records', function (Blueprint $table) {
            
            // Senarai kolum yang kita nak tambah
            $columns = [
                'belanja_jan', 'belanja_feb', 'belanja_mac', 'belanja_apr',
                'belanja_mei', 'belanja_jun', 'belanja_jul', 'belanja_ogos',
                'belanja_sep', 'belanja_okt', 'belanja_nov', 'belanja_dis'
            ];

            $previousColumn = 'belanja_s4'; // Kita akan letak selepas kolum ini

            foreach ($columns as $col) {
                // 🔥 PENYELESAIAN: Hanya tambah jika kolum BELUM wujud
                if (!Schema::hasColumn('kewangan_records', $col)) {
                    
                    // Cek jika kolum 'after' wujud untuk elak error urutan
                    if (Schema::hasColumn('kewangan_records', $previousColumn)) {
                        $table->decimal($col, 15, 2)->default(0)->after($previousColumn);
                    } else {
                        $table->decimal($col, 15, 2)->default(0);
                    }
                }
                
                // Update rujukan supaya bulan seterusnya duduk di sebelah bulan ini
                $previousColumn = $col;
            }
        });
    }

    public function down(): void
    {
        Schema::table('kewangan_records', function (Blueprint $table) {
            $columns = [
                'belanja_jan', 'belanja_feb', 'belanja_mac', 'belanja_apr',
                'belanja_mei', 'belanja_jun', 'belanja_jul', 'belanja_ogos',
                'belanja_sep', 'belanja_okt', 'belanja_nov', 'belanja_dis'
            ];

            // Hanya drop jika wujud
            foreach ($columns as $col) {
                if (Schema::hasColumn('kewangan_records', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};