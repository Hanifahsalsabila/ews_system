<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Vaksin extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = ['kode_vaksin', 'nama_vaksin', 'jenis', 'keterangan'];
}
