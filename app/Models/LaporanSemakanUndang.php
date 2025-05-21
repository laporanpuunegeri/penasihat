<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaporanSemakanUndang extends Model
{
    use HasFactory;

    protected $table = 'laporan_semakan_undang';

    protected $fillable = [
        'tajuk',
        'tindakan',
        'status',
        'user_id',
        'negeri',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
