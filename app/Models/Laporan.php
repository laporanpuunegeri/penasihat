<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Laporan extends Model
{
    use HasFactory;

    protected $fillable = [
        'tarikh',
        'kategori',
        'tajuk',
        'isi_ringkas',
        'tindakan',
        'dirujuk_ke_jpn',
        'user_id',
        'negeri',
    ];

    protected $casts = [
        'tarikh' => 'date',
        'dirujuk_ke_jpn' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
