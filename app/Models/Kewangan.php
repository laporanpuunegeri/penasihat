<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kewangan extends Model
{
    use HasFactory;

    // PENTING: Tetapkan nama table sebab database tuan guna 'kewangan_records'
    protected $table = 'kewangan_records';

    protected $fillable = [
        'user_id',
        'negeri',
        'kod_utama',
        'kod_objek',
        'butiran',
        'peruntukan',
        'belanja', // Jumlah Besar Belanja

        // Senarai Bulan (12 Bulan)
        'belanja_jan',
        'belanja_feb',
        'belanja_mac',
        'belanja_apr',
        'belanja_mei',
        'belanja_jun',
        'belanja_jul',
        'belanja_ogos',
        'belanja_sep',
        'belanja_okt',
        'belanja_nov',
        'belanja_dis',
    ];
}