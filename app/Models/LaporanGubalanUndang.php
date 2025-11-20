<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Laporangubalanundang extends Model
{
    use HasFactory;

    protected $table = 'laporangubalanundangs';

    protected $fillable = [
        'tarikh_daftar',
        'tajuk',
        'tindakan',
        'status',
        'user_id',
        'negeri',
    ];

    protected $casts = [
        'tarikh_daftar' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Hubungan kepada pengguna (yang mendaftarkan laporan)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
