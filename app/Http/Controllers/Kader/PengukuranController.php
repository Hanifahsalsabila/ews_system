<?php

namespace App\Http\Controllers\Kader;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SesiPosyandu;
use App\Models\Absensi;
use App\Models\Warga;
use App\Models\Pemeriksaan;

class PengukuranController extends Controller
{
    public function index()
    {
        $activeSession = SesiPosyandu::whereDate('tanggal', today())->where('status', 'aktif')->first();
        
        $antrean = collect();
        if ($activeSession) {
            // Get all Absensi today where Warga DOES NOT have Pemeriksaan for this session
            $antrean = Absensi::with('warga.balita', 'warga.ibuHamil')
                ->where('sesi_posyandu_id', $activeSession->id)
                ->whereDoesntHave('warga.pemeriksaans', function($q) use ($activeSession) {
                    $q->where('sesi_posyandu_id', $activeSession->id);
                })
                ->get();
        }

        return view('kader.pengukuran.index', compact('activeSession', 'antrean'));
    }

    public function create(Warga $warga)
    {
        $activeSession = SesiPosyandu::whereDate('tanggal', today())->where('status', 'aktif')->first();
        if (!$activeSession) {
            return redirect()->route('kader.pengukuran.index')->with('error', 'Tidak ada sesi aktif.');
        }

        return view('kader.pengukuran.create', compact('warga', 'activeSession'));
    }

    public function store(Request $request, Warga $warga)
    {
        $activeSession = SesiPosyandu::whereDate('tanggal', today())->where('status', 'aktif')->first();
        if (!$activeSession) {
            return redirect()->route('kader.pengukuran.index')->with('error', 'Sesi sudah berakhir.');
        }

        $validated = $request->validate([
            'berat_badan' => 'nullable|numeric',
            'tinggi_badan' => 'nullable|numeric',
            'lingkar_kepala' => 'nullable|numeric',
            'lingkar_lengan' => 'nullable|numeric',
            'tensi_darah' => 'nullable|string',
        ]);

        Pemeriksaan::create([
            'warga_id' => $warga->id,
            'sesi_posyandu_id' => $activeSession->id,
            'berat_badan' => $validated['berat_badan'] ?? null,
            'tinggi_badan' => $validated['tinggi_badan'] ?? null,
            'lingkar_kepala' => $validated['lingkar_kepala'] ?? null,
            'lingkar_lengan' => $validated['lingkar_lengan'] ?? null,
            'tensi_darah' => $validated['tensi_darah'] ?? null,
            'is_validated' => false,
            'petugas_id' => auth()->id()
        ]);

        return redirect()->route('kader.pengukuran.index')->with('success', 'Data pengukuran awal ' . $warga->nama . ' berhasil disimpan dan masuk ke antrean Bidan.');
    }
}
