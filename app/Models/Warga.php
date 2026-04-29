<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use OwenIt\Auditing\Contracts\Auditable;

class Warga extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'warga';

    protected $fillable = [
        'nik',
        'nama',
        'alamat_lengkap',
        'nomor_telepon',
        'region_id'
    ];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function balita()
    {
        return $this->hasOne(Balita::class);
    }

    public function ibuHamil()
    {
        return $this->hasOne(IbuHamil::class);
    }

    public function lansia()
    {
        return $this->hasOne(Lansia::class);
    }

    public function pemeriksaans()
    {
        return $this->hasMany(Pemeriksaan::class);
    }
}
