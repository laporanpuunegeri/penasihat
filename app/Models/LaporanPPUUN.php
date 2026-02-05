<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanPPUUN extends Model
{
    use HasFactory;


    protected $table = 'laporan_p_p_u_u_n_s';

protected $fillable = [
        'tahun', 'outcome_id', 'negeri', 'kpi_desc', 'sasaran_tahunan',
        'suku_1', 'suku_2', 'suku_3', 'suku_4', 
        'catatan_data', 'beban_kes', 
        'user_id', 'status'
    ];

    protected $casts = [
        'catatan_data' => 'array',
        'beban_kes' => 'array', 
        'sasaran_tahunan' => 'integer'
    ];
}