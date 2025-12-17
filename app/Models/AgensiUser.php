<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable; // PENTING: Untuk boleh login
use Illuminate\Notifications\Notifiable;

class AgensiUser extends Authenticatable
{
    use Notifiable;

    protected $table = 'agensi_users'; // Point ke table BM kita

    protected $fillable = [
        'nama_pegawai',
        'email',
        'password',
        'no_telefon',
        'nama_agensi',
        'negeri',
        'status',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];
}