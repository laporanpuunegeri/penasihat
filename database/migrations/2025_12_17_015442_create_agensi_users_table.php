<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
 
public function up()
{
    Schema::create('agensi_users', function (Blueprint $table) {
        $table->id();
        $table->string('nama_pegawai');
        $table->string('email')->unique();
        $table->string('password');
        $table->string('no_telefon');
        $table->string('nama_agensi'); 
        $table->string('negeri');      
        $table->string('status')->default('pending'); 
        $table->timestamps();
    });
}
    public function down(): void
    {
        Schema::dropIfExists('agensi_users');
    }
};
