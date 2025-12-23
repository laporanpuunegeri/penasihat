<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermohonanSeksyen64 extends Model
{
    use HasFactory;

    // Nama table dalam database
    protected $table = 'permohonan_seksyen64s';

    // Benarkan semua column diisi (Mass Assignment)
    protected $guarded = [];

    // Hubungan dengan Agensi (Jika perlu)
    public function agensi()
    {
        return $this->belongsTo(AgensiUser::class, 'agensi_id');
    }
}