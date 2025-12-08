<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DbusPecahanOS29 extends Model
{
    use HasFactory;
    protected $table = 'dbus_pecahan_os29';
    protected $fillable = [
        'dbus_id', 'kod_ol', 'kod_pecahan_sub', 'tahun', 'butiran', 
        'kuantiti', 'kekerapan', 'anggaran_kos', 'jumlah', 'catatan'
    ];
}