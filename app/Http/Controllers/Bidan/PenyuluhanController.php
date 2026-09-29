<?php

namespace App\Http\Controllers\Bidan;

use App\Http\Controllers\Controller;
use App\Models\Penyuluhan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PenyuluhanController extends Controller
{
    public function index()
    {
        $penyuluhans = Penyuluhan::where('penulis_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('bidan.penyuluhan.index', compact('penyuluhans'));
    }

    public function create()
    {
        $kategoris = Penyuluhan::KATEGORI_LIST;

        return view('bidan.penyuluhan.create', compact('kategoris'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'konten' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'kategori' => 'required|in:'.implode(',', array_keys(Penyuluhan::KATEGORI_LIST)),
        ]);

        $data = $request->only('judul', 'konten', 'kategori');
        $data['penulis_id'] = auth()->id();

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('penyuluhan', 'public');
        }

        if ($request->boolean('is_published')) {
            $data['is_published'] = true;
            $data['published_at'] = now();
        }

        Penyuluhan::create($data);

        return redirect()->route('bidan.penyuluhan.index')->with('success', 'Materi penyuluhan berhasil dibuat.');
    }

    public function edit(Penyuluhan $penyuluhan)
    {
        if ($penyuluhan->penulis_id !== auth()->id()) {
            abort(403);
        }

        $kategoris = Penyuluhan::KATEGORI_LIST;

        return view('bidan.penyuluhan.edit', compact('penyuluhan', 'kategoris'));
    }

    public function update(Request $request, Penyuluhan $penyuluhan)
    {
        if ($penyuluhan->penulis_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'judul' => 'required|string|max:255',
            'konten' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'kategori' => 'required|in:'.implode(',', array_keys(Penyuluhan::KATEGORI_LIST)),
        ]);

        $data = $request->only('judul', 'konten', 'kategori');

        if ($request->hasFile('gambar')) {
            // Delete old image
            if ($penyuluhan->gambar) {
                Storage::disk('public')->delete($penyuluhan->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('penyuluhan', 'public');
        }

        if ($request->has('hapus_gambar') && ! $request->hasFile('gambar')) {
            if ($penyuluhan->gambar) {
                Storage::disk('public')->delete($penyuluhan->gambar);
            }
            $data['gambar'] = null;
        }

        $penyuluhan->update($data);

        return redirect()->route('bidan.penyuluhan.index')->with('success', 'Materi penyuluhan berhasil diperbarui.');
    }

    public function togglePublish(Penyuluhan $penyuluhan)
    {
        if ($penyuluhan->penulis_id !== auth()->id()) {
            abort(403);
        }

        $penyuluhan->update([
            'is_published' => ! $penyuluhan->is_published,
            'published_at' => ! $penyuluhan->is_published ? now() : $penyuluhan->published_at,
        ]);

        $status = $penyuluhan->is_published ? 'dipublikasikan' : 'disimpan sebagai draft';

        return back()->with('success', "Materi berhasil {$status}.");
    }

    public function destroy(Penyuluhan $penyuluhan)
    {
        if ($penyuluhan->penulis_id !== auth()->id()) {
            abort(403);
        }

        if ($penyuluhan->gambar) {
            Storage::disk('public')->delete($penyuluhan->gambar);
        }

        $penyuluhan->delete();

        return redirect()->route('bidan.penyuluhan.index')->with('success', 'Materi penyuluhan berhasil dihapus.');
    }
}
