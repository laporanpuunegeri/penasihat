<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermohonanSeksyen326 extends Model
{
    use HasFactory;
    protected $table = 'permohonan_seksyen326';
    
protected $fillable = [
    'agensi_id', 'no_fail', 'nama_penerima', 'ic_penerima', 'alamat_penerima',
    'no_perserahan_kaveat', 'nama_pemohon', 
    'jenis_kawasan', 'nama_kawasan', 
    'jenis_lot', 'no_lot',           
    'jenis_hakmilik', 'no_hakmilik', 'status', 'tarikh_notis', 'nama_pentadbir'
    ];

    protected $casts = [
        'tarikh_notis' => 'date',
    ];

    public function agensi()
    {
        return $this->belongsTo(AgensiUser::class, 'agensi_id');
    }

}