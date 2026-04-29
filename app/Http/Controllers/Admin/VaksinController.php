<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vaksin;

class VaksinController extends Controller
{
    public function index()
    {
        $vaksins = Vaksin::latest()->paginate(10);
        return view('admin.vaksin.index', compact('vaksins'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_vaksin' => 'required|string|unique:vaksins,kode_vaksin|max:50',
            'nama_vaksin' => 'required|string|max:255',
            'jenis' => 'required|in:imunisasi,vitamin,pmt',
            'keterangan' => 'nullable|string'
        ]);

        Vaksin::create($validated);
        return redirect()->back()->with('success', 'Master Data Vaksin berhasil ditambahkan.');
    }

    public function update(Request $request, Vaksin $vaksin)
    {
        $validated = $request->validate([
            'kode_vaksin' => 'required|string|max:50|unique:vaksins,kode_vaksin,' . $vaksin->id,
            'nama_vaksin' => 'required|string|max:255',
            'jenis' => 'required|in:imunisasi,vitamin,pmt',
            'keterangan' => 'nullable|string'
        ]);

        $vaksin->update($validated);
        return redirect()->back()->with('success', 'Master Data Vaksin berhasil diperbarui.');
    }

    public function destroy(Vaksin $vaksin)
    {
        $vaksin->delete();
        return redirect()->back()->with('success', 'Master Data Vaksin berhasil dihapus.');
    }
}
