<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermohonanSeksyen97 extends Model
{
    use HasFactory;

    // Pastikan nama table sama dengan migration (ada 's' kat belakang)
    protected $table = 'permohonan_seksyen97s';

    // Benarkan semua column diisi
    protected $guarded = [];

    // Kalau abang nak link dengan AgensiUser (optional)
    public function agensi()
    {
        return $this->belongsTo(AgensiUser::class, 'agensi_id');
    }
}