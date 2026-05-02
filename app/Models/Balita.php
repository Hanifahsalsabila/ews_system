<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Balita extends Model
{
    use SoftDeletes;

    protected $table = 'balita';

    protected $fillable = [
        'warga_id',
        'jenis_kelamin',
        'tanggal_lahir',
        'nama_ayah',
        'nama_ibu'
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
        return (int) \Carbon\Carbon::parse($this->tanggal_lahir)->diffInMonths(now());
    }
}
