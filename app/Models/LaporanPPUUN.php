<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanPPUUN extends Model
{
 use HasFactory;

    // Sifat-sifat (properties) MESTI berada di dalam kurungan kurawal kelas
    protected $fillable = [
'tahun', 'outcome_id', 'kpi_desc', 'sasaran_tahunan',
'suku_1', 'suku_2', 'suku_3', 'suku_4', 'catatan_data', 'user_id', 'status'
    ];

    protected $casts = [
'catatan_data' => 'array',
'sasaran_tahunan' => 'integer'
    ];
} 