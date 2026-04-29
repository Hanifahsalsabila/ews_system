<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Region;

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
            'name' => 'required|string|max:255',
            'type' => 'required|in:dusun,rw,rt',
            'parent_name' => 'nullable|string',
        ]);

        $parentId = null;
        if (!empty($validated['parent_name'])) {
            $parent = Region::where('name', $validated['parent_name'])->first();
            if ($parent) {
                $parentId = $parent->id;
            } else {
                return back()->withErrors(['parent_name' => 'Induk wilayah (' . $validated['parent_name'] . ') tidak ditemukan di sistem.']);
            }
        }

        Region::create([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'parent_id' => $parentId,
        ]);

        return redirect()->back()->with('success', 'Wilayah berhasil ditambahkan.');
    }

    public function update(Request $request, Region $region)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:dusun,rw,rt',
            'parent_name' => 'nullable|string',
        ]);

        $parentId = null;
        if (!empty($validated['parent_name'])) {
            $parent = Region::where('name', $validated['parent_name'])->first();
            if ($parent) {
                $parentId = $parent->id;
            } else {
                return back()->withErrors(['parent_name' => 'Induk wilayah (' . $validated['parent_name'] . ') tidak ditemukan di sistem.']);
            }
        }

        $region->update([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'parent_id' => $parentId,
        ]);

        return redirect()->back()->with('success', 'Wilayah berhasil diperbarui.');
    }

    public function destroy(Region $region)
    {
        $region->delete();

        return redirect()->back()->with('success', 'Wilayah berhasil dihapus.');
    }
}
