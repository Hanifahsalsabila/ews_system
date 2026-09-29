<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Penyuluhan extends Model
{
    protected $fillable = [
        'judul',
        'konten',
        'gambar',
        'kategori',
        'penulis_id',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public const KATEGORI_LIST = [
        'gizi' => 'Gizi & Nutrisi',
        'imunisasi' => 'Imunisasi',
        'kebersihan' => 'Kebersihan & Sanitasi',
        'ibu_hamil' => 'Ibu Hamil',
        'balita' => 'Balita & Anak',
        'lansia' => 'Lansia',
        'umum' => 'Umum',
    ];

    public function penulis()
    {
        return $this->belongsTo(User::class, 'penulis_id');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function getExcerptAttribute(): string
    {
        return Str::limit(strip_tags($this->konten), 120);
    }

    public function getKategoriLabelAttribute(): string
    {
        return self::KATEGORI_LIST[$this->kategori] ?? ucfirst($this->kategori);
    }
}
