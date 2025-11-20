<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaporanMesyuarat extends Model
{
    use HasFactory;

    protected $table = 'laporan_mesyuarats';

    protected $fillable = [
        'mesyuarat',
        'isu',
        'tarikh_mesyuarat',
        'status',
        'pandangan',
        'user_id',
        'negeri',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'tarikh_mesyuarat' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault([
            'name' => '-'
        ]);
    }
}
