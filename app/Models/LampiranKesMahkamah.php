<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LampiranKesMahkamah extends Model
{
    use HasFactory;

    protected $table = 'lampiran_kes_mahkamah';

    protected $fillable = [
        'user_id',
        'negeri',
        'kategori',
        'bil_aktif',
        'majistret',
        'sesi',
        'tinggi',
        'rayuan',
        'persk',
        'status',
        'bulan',
        'tahun',
    ];
}
