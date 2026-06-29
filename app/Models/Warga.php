<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use OwenIt\Auditing\Contracts\Auditable;

class Warga extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable, SoftDeletes;

    protected $table = 'warga';

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($warga) {
            // Cascade soft deletes to related records
            if ($warga->balita) {
                $warga->balita()->delete();
            }
            if ($warga->ibuHamil) {
                $warga->ibuHamil()->delete();
            }
            if ($warga->lansia) {
                $warga->lansia()->delete();
            }
            
            // Delete related pemeriksaans
            $warga->pemeriksaans()->delete();
        });
    }

    protected $fillable = [
        'nik',
        'nama',
        'alamat_lengkap',
        'nomor_telepon',
        'jenis_kelamin',
        'tanggal_lahir',
        'region_id',
        'user_id',
    ];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
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

    public function kunjunganRumahs()
    {
        return $this->hasMany(KunjunganRumah::class);
    }
}
