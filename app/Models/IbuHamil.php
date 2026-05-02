<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IbuHamil extends Model
{
    use SoftDeletes;

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
