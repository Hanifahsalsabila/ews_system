<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KalkulasiZScore extends Model
{
    protected $table = 'kalkulasi_zscore';

    protected $fillable = [
        'pemeriksaan_id',
        'status_stunting',
        'status_gizi',
        'zscore_bb_u',
        'zscore_tb_u',
        'zscore_bb_tb',
    ];

    public function pemeriksaan()
    {
        return $this->belongsTo(Pemeriksaan::class, 'pemeriksaan_id');
    }
}
