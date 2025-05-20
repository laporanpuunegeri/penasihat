<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanKesMahkamah extends Model
{
    use HasFactory;

    protected $table = 'laporan_kes_mahkamah';

    protected $fillable = [
        'tarikh_daftar',
        'kategori',
        'jenis_mahkamah',
        'pandangan',
        'isu',
        'status',
        'tarikh_selesai',
        'perkara',
        'jenis_kes',
        'tarikh_sebutan',
        'fakta_ringkas',
        'skop_tugas',
        'ringkasan_hujahan',
    ];
}
