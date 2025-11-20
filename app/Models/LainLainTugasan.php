<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LainLainTugasan extends Model
{
    use HasFactory;

    protected $table = 'lain_lain_tugasans';

    protected $fillable = [
        'perihal',
        'tarikh',
        'tindakan',
        'user_id',
        'negeri',
    ];

    protected $casts = [
        'tarikh' => 'date',
    ];

    public $timestamps = true;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
