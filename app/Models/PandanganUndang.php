<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes; // Tambah ini

class Pergerakan extends Model
{
    use HasFactory, SoftDeletes; // Guna SoftDeletes

    protected $table = 'pergerakan'; // Anggap nama jadual adalah 'pergerakan'

    protected $fillable = [
        'tarikh_mula', 'tarikh_akhir', 'masa_mula', 'masa_akhir',
        'jenis', 'kenderaan', 'tujuan_penggunaan', 'destinasi',
        'catatan', 'nama_pemandu', 'no_kenderaan', 
        'status_cc', 'catatan_cc', 'status_yb',
        'user_id', // Foreign Key
    ];

    protected $casts = [
        'tarikh_mula' => 'date',
        'tarikh_akhir' => 'date',
    ];

    /**
     * Relasi ke Pengguna (Pemohon)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}