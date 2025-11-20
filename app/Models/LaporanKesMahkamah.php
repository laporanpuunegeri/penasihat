<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaporanKesMahkamah extends Model
{
    use HasFactory;

    protected $table = 'laporan_kes_mahkamah';

    protected $fillable = [
        'tarikh_daftar',
        'kategori',
        'jenis_mahkamah',
        'pandangan',
        'isu',
        'status',
        'tarikh_selesai',
        'perkara',
        'jenis_kes',
        'tarikh_sebutan',
        'fakta_ringkas',
        'skop_tugas',
        'ringkasan_hujahan',
        'user_id',
        'created_by',
        'negeri',
    ];

    protected $casts = [
        'tarikh_daftar'   => 'datetime',
        'tarikh_selesai'  => 'date',
        'tarikh_sebutan'  => 'date',
        'created_at'      => 'datetime',
        'updated_at'      => 'datetime',
    ];

    /**
     * Hubungan dengan pengguna pemilik laporan
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Hubungan dengan pengguna yang mencipta laporan (jika berbeza)
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
