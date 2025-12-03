<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DbusPecahanOs15 extends Model
{
    use HasFactory;
    
    protected $table = 'dbus_pecahan_os15'; 
    
    protected $fillable = [
        'tahun',
        'kod_ol',
        'sub_kod',
        'butiran',
        'anggaran',
        'bil_unit',
        'jumlah_pecahan',
        'catatan'
    ];
}