<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
    
        Schema::create('kewangan_records', function (Blueprint $table) {
            
            $table->id(); 
            $table->string('negeri')->nullable()->index();
            $table->string('kod_objek')->nullable();       
            $table->decimal('peruntukan', 15, 2)->default(0); 
            $table->decimal('belanja_jan', 15, 2)->default(0);
            $table->decimal('belanja_feb', 15, 2)->default(0);
            $table->decimal('belanja_mac', 15, 2)->default(0);
            $table->decimal('belanja_apr', 15, 2)->default(0);
            $table->decimal('belanja_mei', 15, 2)->default(0);
            $table->decimal('belanja_jun', 15, 2)->default(0);
            $table->decimal('belanja_jul', 15, 2)->default(0);
            $table->decimal('belanja_ogos', 15, 2)->default(0);
            $table->decimal('belanja_sep', 15, 2)->default(0);
            $table->decimal('belanja_okt', 15, 2)->default(0);
            $table->decimal('belanja_nov', 15, 2)->default(0);
            $table->decimal('belanja_dis', 15, 2)->default(0);

            $table->timestamps(); // Created_at & Updated_at
        });
    }

    public function down()
    {
        // Kalau rollback, kita buang terus table ni
        Schema::dropIfExists('kewangan_records');
    }
};