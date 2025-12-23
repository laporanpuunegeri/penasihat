<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permohonan_seksyen97s', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agensi_id'); 

            // --- 1. MAKLUMAT PEMILIK ---
            // Rujuk Notis Tuntutan (Borang 6A)
            $table->string('nama_pemilik'); 
            $table->string('no_kp_pemilik');
            $table->text('alamat_pemilik');

            // --- 2. MAKLUMAT TANAH ---
            // Rujuk Jadual Tanah
            $table->string('jenis_hakmilik'); 
            $table->string('no_hakmilik');    
            $table->string('no_lot');         
            $table->string('mukim');          
            $table->string('daerah');         

            // --- 3. BUTIRAN TUNGGAKAN (KEWANGAN) ---
            // Guna decimal supaya nilai duit tepat
            $table->decimal('sewa_tahun_semasa', 10, 2); 
            $table->string('tempoh_tunggakan');          
            $table->decimal('jumlah_tunggakan', 10, 2);  
            $table->decimal('denda', 10, 2);             
            $table->decimal('kos_notis', 10, 2);         
            $table->decimal('jumlah_besar', 10, 2);      

            // --- 4. MAKLUMAT TAMBAHAN (BANK/GADAIAN) ---
            // Nullable sebab tak semua tanah ada gadaian bank
            $table->string('nama_bank')->nullable();
            $table->text('alamat_bank')->nullable();

            // --- 5. PENTADBIRAN ---
            $table->string('no_fail');         
            $table->date('tarikh_notis');      
            $table->string('nama_pentadbir');  
            
            $table->string('status')->default('Baru');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permohonan_seksyen97s');
    }
};