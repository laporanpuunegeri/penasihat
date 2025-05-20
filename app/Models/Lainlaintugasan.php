<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LainLainTugasan extends Model
{
    use HasFactory;

    protected $table = 'lain_lain_tugasans';

    protected $fillable = [
        'perihal',
        'tarikh',
        'tindakan',
    ];

    public $timestamps = true;
}
