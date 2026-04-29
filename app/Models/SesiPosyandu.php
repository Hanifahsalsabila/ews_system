<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SesiPosyandu extends Model
{
    protected $table = 'sesi_posyandu';

    protected $fillable = [
        'tanggal',
        'nama_sesi',
        'status',
    ];

    public function pemeriksaans()
    {
        return $this->hasMany(Pemeriksaan::class, 'sesi_posyandu_id');
    }

    public function absensis()
    {
        return $this->hasMany(Absensi::class, 'sesi_posyandu_id');
    }
}
