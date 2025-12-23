<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermohonanSeksyen175d extends Model
{
    use HasFactory;
    protected $table = 'permohonan_seksyen175d';
    protected $fillable = [
        'agensi_id', 'no_fail', 'daerah', 'mukim', 'jenis_hakmilik', 
        'no_hakmilik', 'no_lot', 'luas', 'nama_pemilik', 'no_kp_pemilik', 
        'bahagian_tanah', 'status', 'tarikh_notis', 'nama_pentadbir'
    ];

    public function agensi()
    {
        return $this->belongsTo(AgensiUser::class, 'agensi_id');
    }

}