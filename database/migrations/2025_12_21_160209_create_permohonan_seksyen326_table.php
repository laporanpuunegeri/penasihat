<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void
{
    Schema::create('permohonan_seksyen326', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('agensi_id');
        $table->string('no_fail')->nullable();
        
        // Maklumat Penerima
        $table->string('nama_penerima');
        $table->string('ic_penerima')->nullable();
        $table->text('alamat_penerima');
        
        // Maklumat Kaveat
        $table->string('no_perserahan_kaveat');
        $table->string('nama_pemohon');
        
        // MAKLUMAT TANAH (YANG DIKEMASKINI)
        // Kita simpan jenis pilihan (Bandar/Pekan/Mukim)
        $table->string('jenis_kawasan'); // Value: 'Bandar', 'Pekan', atau 'Mukim'
        $table->string('nama_kawasan');  // Value: Nama tempat (contoh: Cheng)
        
        // Kita simpan jenis lot (Lot/Petak/PT)
        $table->string('jenis_lot');     // Value: 'Lot', 'Petak', 'PT', atau 'Plot'
        $table->string('no_lot');        // Value: Nombor (contoh: 3292)

        $table->string('jenis_hakmilik');
        $table->string('no_hakmilik');

        // Pentadbiran
        $table->string('status')->default('Baru');
        $table->date('tarikh_notis');
        $table->string('nama_pentadbir')->default('MOHD HAIRY BIN JAPAH');
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('permohonan_seksyen326');
}
};
