<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KunjunganRumah extends Model
{
    protected $table = 'kunjungan_rumahs';

    protected $fillable = [
        'warga_id',
        'bidan_id',
        'tipe',
        'prioritas',
        'tanggal_rencana',
        'tanggal_kunjungan',
        'status',
        'catatan_sebelum',
        'catatan_hasil',
        'tindak_lanjut',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_rencana' => 'date',
            'tanggal_kunjungan' => 'date',
        ];
    }

    public function warga()
    {
        return $this->belongsTo(Warga::class);
    }

    public function bidan()
    {
        return $this->belongsTo(User::class, 'bidan_id');
    }
}
