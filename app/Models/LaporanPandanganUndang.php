<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaporanPandanganUndang extends Model
{
    use HasFactory;

    protected $table = 'laporan_pandangan_undangs';

    protected $fillable = [
        'kategori',
        'isu',
        'tarikh_terima',
        'fakta_ringkasan',
        'isu_detail',
        'ringkasan_pandangan',
        'jenis_pandangan',
        'status',
        'tarikh_selesai',
        'belum_selesai',
        'dirujuk_jpn',
        'created_by',
        'boss_id',
        'user_id',
        'negeri',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'jenis_pandangan' => 'array',
        'tarikh_terima' => 'date',
        'tarikh_selesai' => 'date',
        'belum_selesai' => 'boolean',
        'dirujuk_jpn' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function boss(): BelongsTo
    {
        return $this->belongsTo(User::class, 'boss_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
