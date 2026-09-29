<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RegionController extends Controller
{
    public function index()
    {
        $regions = Region::with('parent')->latest()->paginate(10);
        $allRegions = Region::all();

        return view('admin.regions.index', compact('regions', 'allRegions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('regions')->where(function ($query) use ($request) {
                    return $query->where('type', $request->type)
                        ->where('parent_id', $request->parent_id);
                }),
            ],
            'type' => 'required|in:dusun,rw,rt',
            'parent_id' => 'required_unless:type,dusun|nullable|exists:regions,id',
        ], [
            'name.unique' => 'Nama wilayah tersebut sudah terdaftar di lokasi induk ini.',
            'parent_id.required_unless' => 'Induk Wilayah wajib dipilih untuk tipe RW dan RT.',
        ]);

        Region::create([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'parent_id' => $validated['parent_id'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Wilayah berhasil ditambahkan.');
    }

    public function update(Request $request, Region $region)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('regions')->where(function ($query) use ($request) {
                    return $query->where('type', $request->type)
                        ->where('parent_id', $request->parent_id);
                })->ignore($region->id),
            ],
            'type' => 'required|in:dusun,rw,rt',
            'parent_id' => 'required_unless:type,dusun|nullable|exists:regions,id',
        ], [
            'name.unique' => 'Nama wilayah tersebut sudah terdaftar di lokasi induk ini.',
            'parent_id.required_unless' => 'Induk Wilayah wajib dipilih untuk tipe RW dan RT.',
        ]);

        $region->update([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'parent_id' => $validated['parent_id'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Wilayah berhasil diperbarui.');
    }

    public function destroy(Region $region)
    {
        $region->delete();

        return redirect()->back()->with('success', 'Wilayah berhasil dihapus.');
    }
}
