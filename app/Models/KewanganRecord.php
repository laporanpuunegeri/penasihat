<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KewanganRecord extends Model
{
    use HasFactory;

    protected $table = 'kewangan_records'; 

    protected $fillable = [
        'negeri',
        'kod_utama',
        'kod_objek',
        'butiran',
        'peruntukan',
        'belanja',     
        'belanja_s1',  
        'belanja_s2', 
        'belanja_s3',  
        'belanja_s4', 
        'tahun'
    ];
}