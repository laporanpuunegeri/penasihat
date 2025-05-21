<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Laporangubalanundang extends Model
{
    use HasFactory;

    // Padankan dengan nama jadual sebenar dalam PostgreSQL
    protected $table = 'laporangubalanundangs';

    // Senaraikan field yang boleh diisi secara mass assignment
    protected $fillable = [
        'tarikh_daftar',
        'tajuk',
        'tindakan',
        'status',
        'user_id',
        'negeri',
    ];

    // Format type casting automatik untuk tarikh
    protected $casts = [
        'tarikh_daftar' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Perhubungan ke model User (untuk user_id)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
