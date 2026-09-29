<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Balita extends Model
{
    use SoftDeletes;

    protected $table = 'balita';

    protected $fillable = [
        'warga_id',
        'nama_ayah',
        'nama_ibu',
    ];

    public function warga()
    {
        return $this->belongsTo(Warga::class);
    }

    /**
     * Hitung umur balita dalam bulan.
     */
    public function getUmurBulanAttribute(): int
    {
        return (int) Carbon::parse($this->warga->tanggal_lahir)->diffInMonths(now());
    }
}
