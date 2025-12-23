<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermohonanSeksyen175a extends Model
{
    use HasFactory;
    protected $table = 'permohonan_seksyen175a';
    protected $fillable = [
        'agensi_id', 'no_fail', 'jenis_hakmilik', 'no_hakmilik', 'no_lot', 
        'luas', 'mukim', 'daerah', 'sebab_penyediaan', 'status', 'tarikh_notis', 'nama_pentadbir'
    ];
    
public function agensi()
    {
        return $this->belongsTo(AgensiUser::class, 'agensi_id');
    }

}