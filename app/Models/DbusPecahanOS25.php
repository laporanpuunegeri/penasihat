<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DbusPecahanOS25 extends Model
{
    use HasFactory;

    // Nama jadual di database
    protected $table = 'dbus_pecahan_os25';

    protected $primaryKey = 'id';
    
    // Senarai field yang boleh diisi secara pukal (mass assignment)
    protected $fillable = [
        'dbus_id',
        'kod_ol',
        'kod_pecahan_sub',
        'tahun',
        'butiran',
        'kuantiti',
        'bil_bulan',
        'anggaran_sebulan',
        'jumlah',
        'catatan',
    ];

    // Konfigurasi data type untuk simpan dalam DB
    protected $casts = [
        'kuantiti' => 'decimal:2',
        'anggaran_sebulan' => 'decimal:2',
        'jumlah' => 'decimal:2',
    ];

    // Hubungan ke rekod Induk Dbus (Optional)
    public function masterDbus()
    {
        return $this->belongsTo(Dbus::class, 'dbus_id'); 
    }
}