<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermohonanSeksyen263 extends Model
{
    use HasFactory;

    // Paksa model guna nama table yang betul
    protected $table = 'permohonan_seksyen263';

    protected $fillable = [
        'agensi_id', 'no_fail', 'nama_pentadbir_tanah', 'ic_pentadbir', 'tarikh_lelongan',
        'hari_lelongan', 'masa_lelongan', 'tempat_lelongan', 'harga_rizab', 'deposit_sepuluh_peratus',
        'amaun_hutang', 'nama_pemegang_gadai', 'tarikh_akhir_bayaran', 'mukim', 'no_lot',
        'jenis_hakmilik', 'no_hakmilik', 'bahagian_tanah', 'no_daftar_gadaian', 'status', 'tarikh_perintah'
    ];

    // Casts ni penting supaya Laravel faham data ni adalah Tarikh & Nombor (Decimal)
    protected $casts = [
        'tarikh_lelongan' => 'date',
        'tarikh_akhir_bayaran' => 'date',
        'tarikh_perintah' => 'date',
        'harga_rizab' => 'decimal:2',
        'deposit_sepuluh_peratus' => 'decimal:2',
        'amaun_hutang' => 'decimal:2',
    ];
    
public function agensi()
    {
        return $this->belongsTo(AgensiUser::class, 'agensi_id');
    }

}