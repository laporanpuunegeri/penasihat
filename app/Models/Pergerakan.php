<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Pergerakan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'tarikh', 'jenis', 'catatan',
    ];

    /**
     * Pergerakan dimiliki oleh seorang pengguna.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
