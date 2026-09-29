<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventaris;
use Illuminate\Http\Request;

class InventarisController extends Controller
{
    public function index()
    {
        $inventaris = Inventaris::latest()->paginate(10);

        return view('admin.inventaris.index', compact('inventaris'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_alat' => 'required|string|unique:inventaris,kode_alat|max:50',
            'nama_alat' => 'required|string|max:255',
            'kondisi' => 'required|in:baik,rusak_ringan,rusak_berat',
            'jumlah' => 'required|integer|min:1',
        ]);

        Inventaris::create($validated);

        return redirect()->back()->with('success', 'Aset Inventaris berhasil ditambahkan.');
    }

    public function update(Request $request, Inventaris $inventari) // default route binding plural inventaris -> inventari
    {
        $validated = $request->validate([
            'kode_alat' => 'required|string|max:50|unique:inventaris,kode_alat,'.$inventari->id,
            'nama_alat' => 'required|string|max:255',
            'kondisi' => 'required|in:baik,rusak_ringan,rusak_berat',
            'jumlah' => 'required|integer|min:0',
        ]);

        $inventari->update($validated);

        return redirect()->back()->with('success', 'Aset Inventaris berhasil diperbarui.');
    }

    public function destroy(Inventaris $inventari)
    {
        $inventari->delete();

        return redirect()->back()->with('success', 'Aset Inventaris berhasil dihapus.');
    }
}
