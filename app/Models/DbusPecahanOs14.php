<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DbusPecahanOs14 extends Model
{
    use HasFactory;
    
    // Set nama jadual secara eksplisit
    protected $table = 'dbus_pecahan_os14'; 
    
    protected $fillable = [
        'tahun',
        'gred',
        'anggaran',
        'bil_orang',
        'bil_bulan',
        'jumlah_pecahan',
        'catatan'
    ];
}