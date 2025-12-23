<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermohonanSeksyen62 extends Model
{
    use HasFactory;

    protected $table = 'permohonan_seksyen62';

    protected $guarded = [];

    public function agensi()
    {
        return $this->belongsTo(AgensiUser::class, 'agensi_id');
    }
}