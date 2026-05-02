<?php

namespace App\Http\Controllers\Kader;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SesiPosyandu;
use App\Models\Absensi;
use App\Models\Warga;
use App\Models\Pemeriksaan;
use App\Models\KunjunganRumah;

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

        $pemeriksaan = Pemeriksaan::create([
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

        // ── Early Warning System (EWS): Deteksi Penurunan Berat Badan 2T ──
        $this->checkEarlyWarning($warga, $pemeriksaan);

        return redirect()->route('kader.pengukuran.index')->with('success', 'Data pengukuran awal ' . $warga->nama . ' berhasil disimpan dan masuk ke antrean Bidan.');
    }

    /**
     * Deteksi penurunan berat badan 2 bulan berturut-turut (2T).
     * Jika terdeteksi, otomatis buat entri kunjungan rumah untuk Bidan.
     */
    private function checkEarlyWarning(Warga $warga, Pemeriksaan $currentPemeriksaan): void
    {
        // Hanya cek untuk balita
        if (!$warga->balita) {
            return;
        }

        // Ambil 3 pemeriksaan terakhir (termasuk yang baru disimpan)
        $lastThree = Pemeriksaan::where('warga_id', $warga->id)
            ->whereNotNull('berat_badan')
            ->orderByDesc('created_at')
            ->take(3)
            ->pluck('berat_badan')
            ->toArray();

        // Minimal 3 data untuk mendeteksi pola "2T" (bulan ini < bulan lalu < 2 bulan lalu)
        if (count($lastThree) < 3) {
            return;
        }

        $bulanIni = $lastThree[0];
        $bulanLalu = $lastThree[1];
        $duaBulanLalu = $lastThree[2];

        // Cek apakah berat badan turun/stagnan 2 bulan berturut-turut
        $isTwoMonthDecline = ($bulanIni <= $bulanLalu) && ($bulanLalu <= $duaBulanLalu);

        if ($isTwoMonthDecline) {
            // Cek apakah sudah ada kunjungan yang dijadwalkan dan belum selesai
            $existingVisit = KunjunganRumah::where('warga_id', $warga->id)
                ->whereIn('status', ['dijadwalkan', 'ditunda'])
                ->where('tipe', '2t')
                ->exists();

            if (!$existingVisit) {
                KunjunganRumah::create([
                    'warga_id' => $warga->id,
                    'tipe' => '2t',
                    'prioritas' => 'tinggi',
                    'tanggal_rencana' => now()->addDays(3)->toDateString(),
                    'status' => 'dijadwalkan',
                    'catatan_sebelum' => "⚠️ PERINGATAN OTOMATIS (EWS): Berat badan {$warga->nama} turun/stagnan 2 bulan berturut-turut. "
                        . "BB saat ini: {$bulanIni} kg, bulan lalu: {$bulanLalu} kg, 2 bulan lalu: {$duaBulanLalu} kg. "
                        . "Segera lakukan kunjungan rumah untuk intervensi gizi.",
                ]);
            }
        }
    }
}
