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
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'tarikh_daftar' => 'datetime',
        'tarikh_selesai' => 'date',
        'tarikh_sebutan' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
