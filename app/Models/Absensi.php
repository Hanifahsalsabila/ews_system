<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    protected $table = 'absensi';

    protected $fillable = [
        'warga_id',
        'sesi_posyandu_id',
        'status',
    ];

    public function warga()
    {
        return $this->belongsTo(Warga::class);
    }

    public function sesiPosyandu()
    {
        return $this->belongsTo(SesiPosyandu::class, 'sesi_posyandu_id');
    }
}
