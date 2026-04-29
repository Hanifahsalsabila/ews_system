<?php

namespace App\Http\Controllers\Kader;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Warga;
use App\Models\Region;
use App\Models\Balita;
use App\Models\IbuHamil;
use App\Models\Lansia;
use Illuminate\Support\Facades\DB;

class WargaController extends Controller
{
    public function index(Request $request)
    {
        $query = Warga::with(['region', 'balita', 'ibuHamil', 'lansia']);

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $wargas = $query->orderBy('nama')->paginate(15);
        $regions = Region::all();
            
        return view('kader.warga.index', compact('wargas', 'regions'));
    }

    public function create()
    {
        $regions = Region::where('type', 'rt')->orWhere('type', 'rw')->get();
        return view('kader.warga.create', compact('regions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nik' => 'required|string|size:16|unique:warga,nik',
            'nama' => 'required|string|max:255',
            'alamat_lengkap' => 'nullable|string',
            'nomor_telepon' => 'nullable|string|max:20',
            'region_id' => 'required|exists:regions,id',
            'cohort_type' => 'required|in:balita,ibu_hamil,lansia,umum',
            
            // Balita specific
            'jenis_kelamin' => 'required_if:cohort_type,balita|in:L,P',
            'tanggal_lahir' => 'required_if:cohort_type,balita|date',
            'nama_ayah' => 'nullable|string|max:255',
            'nama_ibu' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $warga = Warga::create([
                'nik' => $validated['nik'],
                'nama' => $validated['nama'],
                'alamat_lengkap' => $validated['alamat_lengkap'],
                'nomor_telepon' => $validated['nomor_telepon'],
                'region_id' => $validated['region_id'],
            ]);

            if ($validated['cohort_type'] === 'balita') {
                Balita::create([
                    'warga_id' => $warga->id,
                    'jenis_kelamin' => $validated['jenis_kelamin'],
                    'tanggal_lahir' => $validated['tanggal_lahir'],
                    'nama_ayah' => $validated['nama_ayah'],
                    'nama_ibu' => $validated['nama_ibu'],
                ]);
            } elseif ($validated['cohort_type'] === 'ibu_hamil') {
                IbuHamil::create([
                    'warga_id' => $warga->id,
                ]);
            } elseif ($validated['cohort_type'] === 'lansia') {
                Lansia::create([
                    'warga_id' => $warga->id,
                ]);
            }

            DB::commit();
            return redirect()->route('kader.warga.index')->with('success', 'Warga baru berhasil didaftarkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menyimpan: ' . $e->getMessage()])->withInput();
        }
    }

    public function edit(Warga $warga)
    {
        $regions = Region::where('type', 'rt')->orWhere('type', 'rw')->get();
        // Determine cohort type for the view
        $cohort_type = 'umum';
        if ($warga->balita) $cohort_type = 'balita';
        elseif ($warga->ibuHamil) $cohort_type = 'ibu_hamil';
        elseif ($warga->lansia) $cohort_type = 'lansia';

        return view('kader.warga.edit', compact('warga', 'regions', 'cohort_type'));
    }

    public function update(Request $request, Warga $warga)
    {
        $validated = $request->validate([
            'nik' => 'required|string|size:16|unique:warga,nik,' . $warga->id,
            'nama' => 'required|string|max:255',
            'alamat_lengkap' => 'nullable|string',
            'nomor_telepon' => 'nullable|string|max:20',
            'region_id' => 'required|exists:regions,id',
            
            // Balita updates
            'jenis_kelamin' => 'nullable|in:L,P',
            'tanggal_lahir' => 'nullable|date',
            'nama_ayah' => 'nullable|string|max:255',
            'nama_ibu' => 'nullable|string|max:255',
            
            // Ibu Hamil updates (if needed by Kader)
            'hpht' => 'nullable|date',
            'hpl' => 'nullable|date',
            'kehamilan_ke' => 'nullable|integer|min:1',
            
            // Lansia updates
            'golongan_darah' => 'nullable|string|max:5',
            'riwayat_penyakit' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $warga->update([
                'nik' => $validated['nik'],
                'nama' => $validated['nama'],
                'alamat_lengkap' => $validated['alamat_lengkap'],
                'nomor_telepon' => $validated['nomor_telepon'],
                'region_id' => $validated['region_id'],
            ]);

            if ($warga->balita) {
                $warga->balita->update([
                    'jenis_kelamin' => $validated['jenis_kelamin'],
                    'tanggal_lahir' => $validated['tanggal_lahir'],
                    'nama_ayah' => $validated['nama_ayah'],
                    'nama_ibu' => $validated['nama_ibu'],
                ]);
            } elseif ($warga->ibuHamil) {
                $warga->ibuHamil->update([
                    'hpht' => $validated['hpht'],
                    'hpl' => $validated['hpl'],
                    'kehamilan_ke' => $validated['kehamilan_ke'] ?? $warga->ibuHamil->kehamilan_ke,
                ]);
            } elseif ($warga->lansia) {
                $warga->lansia->update([
                    'golongan_darah' => $validated['golongan_darah'],
                    'riwayat_penyakit' => $validated['riwayat_penyakit'],
                ]);
            }

            DB::commit();
            return redirect()->route('kader.warga.index')->with('success', 'Data Warga berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal memperbarui: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy(Warga $warga)
    {
        $warga->delete();
        return redirect()->route('kader.warga.index')->with('success', 'Data Warga berhasil dihapus.');
    }
}
