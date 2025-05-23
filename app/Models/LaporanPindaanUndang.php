<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaporanPindaanUndang extends Model
{
    use HasFactory;

    protected $table = 'laporan_pindaan_undangs';

    protected $fillable = [
        'tajuk',
        'tindakan',
        'status',
        'user_id',
        'negeri',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Pegawai yang mencipta laporan ini.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault([
            'name' => '(Pengguna Tidak Dikenal Pasti)'
        ]);
    }
}
