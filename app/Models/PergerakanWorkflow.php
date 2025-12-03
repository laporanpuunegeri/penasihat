<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pergerakan extends Model
{
    use HasFactory;

    protected $table = 'pergerakans';

    protected $fillable = [
        'user_id',
        'tarikh_mula',
        'tarikh_akhir',
        'jenis',
        'kenderaan',
        'catatan',
        'tujuan_penggunaan', 
        'destinasi',
        'masa_mula',
        'masa_akhir',
        
        // Workflow Columns
        'cc_id',
        'yb_id',
        'no_kenderaan',
        'nama_pemandu',
        'status_cc',
        'status_yb',
        'catatan_cc',
        'catatan_yb',
        'tarikh_cc',
        'tarikh_yb',
    ];

    // Hubungan ke Pemohon (User)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // 🔥 HUBUNGAN KRITIKAL: Pegawai Penyokong (CC) - MEMBETULKAN RALAT
    public function cc()
    {
        return $this->belongsTo(User::class, 'cc_id');
    }

    // 🔥 HUBUNGAN KRITIKAL: Pelulus (YB)
    public function yb()
    {
        return $this->belongsTo(User::class, 'yb_id');
    }
}