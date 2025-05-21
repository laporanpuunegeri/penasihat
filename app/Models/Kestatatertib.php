<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kestatatertib extends Model
{
    use HasFactory;

    protected $table = 'kestatatertib';

    protected $fillable = [
        'tarikh_terima',
        'kategori',
        'fakta_ringkasan',
        'isu',
        'ringkasan_pandangan',
        'status',
        'tarikh_selesai',
        'hantar_kepada_boss',
        'tarikh_daftar',
        'user_id',
        'negeri',
    ];

    protected $casts = [
        'tarikh_terima' => 'date',
        'tarikh_selesai' => 'date',
        'tarikh_daftar' => 'datetime',
        'hantar_kepada_boss' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
