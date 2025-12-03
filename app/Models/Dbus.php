<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dbus extends Model
{
    use HasFactory;
    
    protected $table = 'dbus'; 
    
    protected $fillable = ['kod_objek', 'perkara', 'jenis', 'jumlah', 'tahun'];
}