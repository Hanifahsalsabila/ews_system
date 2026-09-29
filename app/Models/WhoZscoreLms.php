<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhoZscoreLms extends Model
{
    protected $table = 'who_zscore_lms';

    protected $fillable = [
        'standard',
        'indicator',
        'gender',
        'ref_value',
        'unit',
        'l',
        'm',
        's',
    ];
}

