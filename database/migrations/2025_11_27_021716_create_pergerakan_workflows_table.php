<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pergerakan_workflows', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pergerakan_id'); // link ke table pergerakans
            $table->enum('status_cc', ['Pending', 'Sokong', 'Tolak'])->default('Pending');
            $table->enum('status_yb', ['Pending', 'Lulus', 'Tolak'])->default('Pending');
            $table->text('catatan_cc')->nullable();
            $table->text('catatan_yb')->nullable();
            $table->unsignedBigInteger('user_id')->nullable(); // siapa buat approval
            $table->timestamps();

            $table->foreign('pergerakan_id')->references('id')->on('pergerakans')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pergerakan_workflows');
    }
};
