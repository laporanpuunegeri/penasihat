<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PandanganUndang extends Model
{
    use HasFactory;

    protected $table = 'laporan_pandangan_undangs';

    protected $fillable = [
        'kategori',
        'tarikh_terima',
        'fakta_ringkasan',
        'isu',
        'ringkasan_pandangan',
        'jenis',
        'status',
        'tarikh_selesai',
        'dirujuk_jpn',
    ];
}
