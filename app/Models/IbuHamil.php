<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IbuHamil extends Model
{
    protected $table = 'ibu_hamil';

    protected $fillable = [
        'warga_id',
        'hpht',
        'hpl',
        'kehamilan_ke'
    ];

    public function warga()
    {
        return $this->belongsTo(Warga::class);
    }
}
