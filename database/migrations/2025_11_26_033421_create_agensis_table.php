<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('agensis', function (Blueprint $table) {
            $table->id();
            $table->string('nama_agensi');
            $table->string('negeri')->index(); // Kita tambah index supaya laju search
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('agensis');
    }
};