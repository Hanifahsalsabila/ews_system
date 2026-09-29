<?php

namespace App\Http\Controllers\Kader;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\SesiPosyandu;
use App\Models\Warga;
use Illuminate\Http\Request;

class KaderDashboardController extends Controller
{
    public function index()
    {
        // Get active session for today
        $activeSession = SesiPosyandu::whereDate('tanggal', today())->where('status', 'aktif')->first();

        $stats = [
            'total_warga' => Warga::count(),
            'hadir_hari_ini' => $activeSession ? Absensi::where('sesi_posyandu_id', $activeSession->id)->count() : 0,
            'menunggu_pengukuran' => $activeSession ? Absensi::where('sesi_posyandu_id', $activeSession->id)
                ->whereDoesntHave('warga.pemeriksaans', function ($q) use ($activeSession) {
                    $q->where('sesi_posyandu_id', $activeSession->id);
                })->count() : 0,
        ];

        return view('kader.dashboard.index', compact('activeSession', 'stats'));
    }

    public function storeSesi(Request $request)
    {
        $validated = $request->validate([
            'nama_sesi' => 'required|string|max:255',
        ]);

        SesiPosyandu::create([
            'nama_sesi' => $validated['nama_sesi'],
            'tanggal' => today(),
            'status' => 'aktif',
        ]);

        return redirect()->route('kader.dashboard')->with('success', 'Sesi Posyandu untuk hari ini berhasil dibuka.');
    }

    public function toggleSesi(SesiPosyandu $sesi)
    {
        $sesi->status = 'selesai';
        $sesi->save();

        return redirect()->route('kader.dashboard')->with('success', 'Sesi Posyandu berhasil dihentikan (Selesai).');
    }
}
