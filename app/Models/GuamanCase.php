<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuamanCase extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $dates = ['tarikh_buka'];
    protected $table = 'guaman_cases';
}