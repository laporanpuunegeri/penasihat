<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DbusPecahanOS21 extends Model
{
    use HasFactory;

    // Pastikan nama table ini SAMA dengan yang anda buat di migration tadi
    protected $table = 'dbus_pecahan_os21'; 

    protected $guarded = []; 
}