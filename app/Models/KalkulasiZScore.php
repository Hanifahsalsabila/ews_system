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
        'status_bb_tb',
        'zscore_bb_u',
        'raw_zscore_bb_u',
        'flag_bb_u',
        'zscore_tb_u',
        'raw_zscore_tb_u',
        'flag_tb_u',
        'zscore_bb_tb',
        'raw_zscore_bb_tb',
        'flag_bb_tb',
        'age_in_days',
        'measurement_type',
        'source_version',
        'ews_status',
        'ews_message',
        'priority_score',
        'priority_details',
        'alert_status',
        'resolved_at',
        'resolved_by',
    ];

    protected $casts = [
        'flag_bb_u' => 'boolean',
        'flag_tb_u' => 'boolean',
        'flag_bb_tb' => 'boolean',
        'priority_score' => 'float',
        'priority_details' => 'array',
        'resolved_at' => 'datetime',
    ];

    public function pemeriksaan()
    {
        return $this->belongsTo(Pemeriksaan::class, 'pemeriksaan_id');
    }

    public function resolvedBy()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }
}
