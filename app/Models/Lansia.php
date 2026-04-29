<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lansia extends Model
{
    protected $table = 'lansia';

    protected $fillable = [
        'warga_id',
        'golongan_darah',
        'riwayat_penyakit'
    ];

    public function warga()
    {
        return $this->belongsTo(Warga::class);
    }
}
