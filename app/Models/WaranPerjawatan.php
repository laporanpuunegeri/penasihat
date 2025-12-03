<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaranPerjawatan extends Model
{
    use HasFactory;

    // Kekalkan 'waran_perjawatans' mengikut pengesahan terakhir anda
    protected $table = 'waran_perjawatans'; 

    protected $fillable = [
        'jawatan',
        'bil',
        // FIELD BAHARU DITAMBAH UNTUK MASS ASSIGNMENT
        'persekutuan', 
        'negeri',      
        
        'isi',
        'kosong',
        'nota', 
    ];
}