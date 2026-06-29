<?php

namespace App\Http\Controllers\Kader;

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

        $penyuluhans = $query->paginate(10)->withQueryString();
        $kategoris = Penyuluhan::KATEGORI_LIST;

        return view('kader.penyuluhan.index', compact('penyuluhans', 'kategoris'));
    }

    public function show(Penyuluhan $penyuluhan)
    {
        if (!$penyuluhan->is_published) {
            abort(404);
        }

        $penyuluhan->load('penulis');

        return view('kader.penyuluhan.show', compact('penyuluhan'));
    }
}
