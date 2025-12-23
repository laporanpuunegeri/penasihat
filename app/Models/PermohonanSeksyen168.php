<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermohonanSeksyen168 extends Model
{
    use HasFactory;

    protected $table = 'permohonan_seksyen168';

    protected $fillable = [
        'agensi_id',
        'no_fail',
        'nama_pemilik',
        'no_kp_pemilik',
        'alamat_pemilik',
        'jenis_hakmilik',
        'no_hakmilik',
        'no_lot',
        'mukim',
        'daerah',
        'luas',
        'sebab_permohonan',
        'status',
        'tarikh_notis',
        'nama_pentadbir'
    ];
    
public function agensi()
    {
        return $this->belongsTo(AgensiUser::class, 'agensi_id');
    }

}