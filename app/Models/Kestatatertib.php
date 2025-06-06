<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kestatatertib extends Model
{
    use HasFactory;

    // ✅ Gunakan huruf kecil untuk keserasian dengan nama jadual sebenar (MySQL case-sensitive)
    protected $table = 'kestatatertib';

    protected $fillable = [
        'tarikh_terima',
        'kategori',
        'fakta_ringkasan',
        'isu',
        'ringkasan_pandangan',
        'status',              // status sebagai string
        'tarikh_selesai',      // tarikh selesai sebagai date
        'hantar_kepada_boss',
        'tarikh_daftar',
    ];

    protected $casts = [
        'tarikh_terima' => 'date',
        'tarikh_selesai' => 'date',
        'tarikh_daftar' => 'datetime',
        'hantar_kepada_boss' => 'boolean',
    ];
}
