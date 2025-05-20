<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanPandanganUndang extends Model
{
    use HasFactory;

    protected $table = 'laporan_pandangan_undangs';

    protected $fillable = [
        'kategori',
        'isu',
        'tarikh_terima',
        'fakta_ringkasan',
        'isu_detail',
        'ringkasan_pandangan',
        'jenis_pandangan',
        'status',
        'tarikh_selesai',
        'belum_selesai',
        'dirujuk_jpn',
        'created_by',
        'boss_id',
    ];

    protected $casts = [
        'jenis_pandangan' => 'array',
        'tarikh_terima' => 'date',
        'tarikh_selesai' => 'date',
        'belum_selesai' => 'boolean',
        'dirujuk_jpn' => 'boolean',
    ];
}
