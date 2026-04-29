<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Inventaris extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = ['kode_alat', 'nama_alat', 'kondisi', 'jumlah'];
}
