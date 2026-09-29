<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Region extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = ['name', 'type', 'parent_id'];

    public function parent()
    {
        return $this->belongsTo(Region::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Region::class, 'parent_id');
    }

    public function warga()
    {
        return $this->hasMany(Warga::class);
    }

    public function getDusunNameAttribute()
    {
        if ($this->type === 'dusun') {
            return $this->name;
        }
        if ($this->type === 'rw') {
            return $this->parent?->name;
        }
        if ($this->type === 'rt') {
            return $this->parent?->parent?->name;
        }

        return null;
    }

    public function getRwNameAttribute()
    {
        if ($this->type === 'rw') {
            return $this->name;
        }
        if ($this->type === 'rt') {
            return $this->parent?->name;
        }

        return null;
    }

    public function getRtNameAttribute()
    {
        if ($this->type === 'rt') {
            return $this->name;
        }

        return null;
    }
}
