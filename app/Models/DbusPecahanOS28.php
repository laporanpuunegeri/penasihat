<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DbusPecahanOS28 extends Model
{
    use HasFactory;
    protected $table = 'dbus_pecahan_os28';
    protected $fillable = [
        'dbus_id', 'kod_ol', 'kod_pecahan_sub', 'tahun', 'butiran', 
        'kuantiti', 'bil_servis', 'anggaran_kos', 'jumlah', 'catatan'
    ];
}