<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use OwenIt\Auditing\Contracts\Auditable;

class Pemeriksaan extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
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
