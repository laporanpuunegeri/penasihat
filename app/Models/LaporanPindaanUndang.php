<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanPindaanUndang extends Model
{
    use HasFactory;

    protected $table = 'laporan_pindaan_undangs';


    protected $fillable = [
        'tajuk',
        'tindakan',
        'status',
    ];
}
