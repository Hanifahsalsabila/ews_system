<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use OwenIt\Auditing\Contracts\Auditable;

class Pemeriksaan extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable, SoftDeletes;
    protected $table = 'pemeriksaan';

    protected $fillable = [
        'warga_id',
        'sesi_posyandu_id',
        'berat_badan',
        'tinggi_badan',
        'lingkar_kepala',
        'lingkar_lengan',
        'tfu',
        'djj',
        'tensi_darah',
        'keluhan',
        'tindakan',
        'catatan',
        'is_validated',
        'petugas_id',
        // New fields
        'vitamin_a',
        'obat_cacing',
        'asi_eksklusif',
        'pmt',
        'suplementasi_fe',
        'status_tt',
        'gula_darah',
        'kolesterol',
        'asam_urat',
        'imt',
        'tipe_kohort',
    ];

    public function warga()
    {
        return $this->belongsTo(Warga::class);
    }

    public function sesiPosyandu()
    {
        return $this->belongsTo(SesiPosyandu::class, 'sesi_posyandu_id');
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }
}
