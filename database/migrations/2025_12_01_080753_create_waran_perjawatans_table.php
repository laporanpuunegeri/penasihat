<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('waran_perjawatans', function (Blueprint $table) {
            $table->id();
            $table->string('jawatan');
            $table->integer('bil')->default(0); 
            $table->integer('isi')->default(0); 
            $table->integer('kosong')->default(0); 
            $table->timestamps();
            $table->text('nota')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('waran_perjawatans');
    }
};