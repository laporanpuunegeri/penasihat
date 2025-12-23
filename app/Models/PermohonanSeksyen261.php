<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermohonanSeksyen261 extends Model
{
    use HasFactory;
    protected $table = 'permohonan_seksyen261';
    protected $fillable = [
        'agensi_id', 'no_fail', 'nama_penggadai', 'alamat_penggadai', 'nama_pemegang_gadai',
        'tempat_siasatan', 'tarikh_siasatan', 'masa_siasatan', 'mukim', 'no_lot',
        'jenis_hakmilik', 'no_hakmilik', 'bahagian_tanah', 'no_daftar_gadaian',
        'status', 'tarikh_notis', 'nama_pentadbir'
    ];

    public function agensi()
    {
        return $this->belongsTo(AgensiUser::class, 'agensi_id');
    }

}