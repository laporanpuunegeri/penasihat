<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DbusPecahanOS26 extends Model
{
    use HasFactory;
    protected $table = 'dbus_pecahan_os26';
    protected $fillable = [
        'dbus_id', 'kod_ol', 'kod_pecahan_sub', 'tahun', 'butiran', 
        'kuantiti', 'bil_bulan', 'anggaran_sebulan', 'jumlah', 'catatan',
    ];
    protected $casts = [
        'kuantiti' => 'decimal:2', 'anggaran_sebulan' => 'decimal:2', 'jumlah' => 'decimal:2',
    ];
}