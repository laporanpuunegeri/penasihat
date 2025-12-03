<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanMesyuarat extends Model
{
    use HasFactory;

    protected $fillable = [
        'mesyuarat',
        'isu',
        'tarikh_mesyuarat',
        'status',
        'pandangan',
        'user_id',
        'negeri',
    ];

    // Optional: relation to user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
