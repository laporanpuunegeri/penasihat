<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanMesyuarat extends Model
{
    use HasFactory;

    protected $table = 'laporan_mesyuarats'; // pastikan padan dengan nama jadual

    protected $fillable = [
        'mesyuarat',
        'isu',
        'tarikh_mesyuarat',
        'status',
        'pandangan'
    ];
}
