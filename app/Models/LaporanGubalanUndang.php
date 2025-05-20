<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laporangubalanundang extends Model
{
    use HasFactory;

    protected $fillable = ['tajuk', 'tindakan', 'status'];
}
