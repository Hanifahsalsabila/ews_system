<?php

namespace App\Http\Controllers\Bidan;

use App\Http\Controllers\Controller;
use App\Models\KunjunganRumah;
use Illuminate\Http\Request;

class KunjunganRumahController extends Controller
{
    /**
     * Tampilkan daftar kunjungan rumah (semua status).
     */
    public function index(Request $request)
    {
        $query = KunjunganRumah::with('warga.balita', 'warga.region', 'bidan')
            ->orderByRaw("FIELD(prioritas, 'kritis', 'tinggi', 'sedang', 'rendah')")
            ->orderByDesc('created_at');

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan prioritas
        if ($request->filled('prioritas')) {
            $query->where('prioritas', $request->prioritas);
        }

        $kunjungans = $query->paginate(15);

        // Statistik ringkasan
        $stats = [
            'dijadwalkan' => KunjunganRumah::where('status', 'dijadwalkan')->count(),
            'selesai' => KunjunganRumah::where('status', 'selesai')->count(),
            'kritis' => KunjunganRumah::where('prioritas', 'kritis')->whereIn('status', ['dijadwalkan', 'ditunda'])->count(),
            'tinggi' => KunjunganRumah::where('prioritas', 'tinggi')->whereIn('status', ['dijadwalkan', 'ditunda'])->count(),
        ];

        return view('bidan.kunjungan.index', compact('kunjungans', 'stats'));
    }

    /**
     * Ambil / klaim kunjungan oleh bidan tertentu.
     */
    public function claim(KunjunganRumah $kunjungan)
    {
        $kunjungan->update([
            'bidan_id' => auth()->id(),
        ]);

        return redirect()->route('bidan.kunjungan.index')->with('success', 'Kunjungan berhasil diklaim. Silakan atur jadwal kunjungan.');
    }

    /**
     * Tampilkan form follow-up laporan kunjungan rumah.
     */
    public function edit(KunjunganRumah $kunjungan)
    {
        $kunjungan->load('warga.balita', 'warga.pemeriksaans.sesiPosyandu', 'bidan');

        return view('bidan.kunjungan.edit', compact('kunjungan'));
    }

    /**
     * Simpan laporan hasil kunjungan rumah.
     */
    public function update(Request $request, KunjunganRumah $kunjungan)
    {
        $validated = $request->validate([
            'tanggal_kunjungan' => 'required|date',
            'catatan_hasil' => 'required|string|max:2000',
            'tindak_lanjut' => 'nullable|string|max:1000',
            'status' => 'required|in:selesai,ditunda',
        ]);

        $kunjungan->update($validated);

        return redirect()->route('bidan.kunjungan.index')->with('success', 'Laporan kunjungan rumah berhasil disimpan.');
    }
}
