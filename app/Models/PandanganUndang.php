<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PandanganUndang extends Model
{
    use HasFactory;

    protected $table = 'laporan_pandangan_undangs';

    protected $fillable = [
        'kategori',
        'tarikh_terima',
        'fakta_ringkasan',
        'isu',
        'ringkasan_pandangan',
        'jenis',
        'status',
        'tarikh_selesai',
        'dirujuk_jpn',
        'user_id',
        'negeri',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'tarikh_terima' => 'date',
        'tarikh_selesai' => 'date',
        'dirujuk_jpn' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
