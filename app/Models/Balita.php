<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Balita extends Model
{
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
}
