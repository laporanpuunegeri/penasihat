<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanSemakanUndang extends Model
{
    use HasFactory;

    protected $table = 'laporan_semakan_undang';

    protected $fillable = [
        'tajuk',
        'tindakan',
        'status',
    ];
}
