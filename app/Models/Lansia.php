<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lansia extends Model
{
    use SoftDeletes;

    protected $table = 'lansia';

    protected $fillable = [
        'warga_id',
        'golongan_darah',
        'riwayat_penyakit',
    ];

    public function warga()
    {
        return $this->belongsTo(Warga::class);
    }
}
