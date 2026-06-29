<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use App\Models\Penyuluhan;
use Illuminate\Http\Request;

class PenyuluhanController extends Controller
{
    public function index(Request $request)
    {
        $query = Penyuluhan::published()->with('penulis')->latest('published_at');

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        $penyuluhans = $query->paginate(9)->withQueryString();
        $kategoris = Penyuluhan::KATEGORI_LIST;

        return view('warga.penyuluhan.index', compact('penyuluhans', 'kategoris'));
    }

    public function show(Penyuluhan $penyuluhan)
    {
        if (!$penyuluhan->is_published) {
            abort(404);
        }

        $penyuluhan->load('penulis');

        // Related articles
        $related = Penyuluhan::published()
            ->where('kategori', $penyuluhan->kategori)
            ->where('id', '!=', $penyuluhan->id)
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('warga.penyuluhan.show', compact('penyuluhan', 'related'));
    }
}
