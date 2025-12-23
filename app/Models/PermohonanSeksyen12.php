<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PermohonanSeksyen12 extends Model
{
    use HasFactory;

    protected $table = 'permohonan_seksyen12'; 

protected $fillable = [
        'no_kp',    
        'jawatan',  
        'position', 
        'agensi_id', 
        'nama', 
        'pelantikan_bm', 
        'pelantikan_bi', 
        'tarikh_lantikan', 
        'tarikh_tt', 
        'no_fail', 
        'status'
    ];

public function agensi()
{
    return $this->belongsTo(\App\Models\AgensiUser::class, 'agensi_id');
}
}