<?php

namespace App\Http\Controllers\Bidan;

use App\Http\Controllers\Controller;
use App\Models\Inventaris;
use App\Models\Vaksin;
use Illuminate\Http\Request;

class LogistikController extends Controller
{
    public function index()
    {
        $vaksins = Vaksin::all();
        $inventaris = Inventaris::all();

        return view('bidan.logistik.index', compact('vaksins', 'inventaris'));
    }

    public function storeVaksin(Request $request)
    {
        $validated = $request->validate([
            'nama_vaksin' => 'required|string|max:255',
            'batch_number' => 'required|string|max:255',
            'stok' => 'required|integer|min:0',
            'tanggal_kadaluarsa' => 'required|date',
        ]);

        Vaksin::create($validated);

        return redirect()->back()->with('success', 'Data vaksin berhasil ditambahkan.');
    }

    public function storeInventaris(Request $request)
    {
        $validated = $request->validate([
            'nama_alat' => 'required|string|max:255',
            'kode_alat' => 'required|string|max:255|unique:inventaris,kode_alat',
            'jumlah' => 'required|integer|min:0',
            'kondisi' => 'required|string|in:baik,rusak_ringan,rusak_berat',
        ]);

        Inventaris::create($validated);

        return redirect()->back()->with('success', 'Data inventaris berhasil ditambahkan.');
    }
}
