<?php

namespace App\Http\Controllers\Kader;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\KalkulasiZScore;
use App\Models\KunjunganRumah;
use App\Models\Pemeriksaan;
use App\Models\SesiPosyandu;
use App\Models\Warga;
use App\Services\WhoZscoreService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PengukuranController extends Controller
{
    public function index()
    {
        $activeSession = SesiPosyandu::whereDate('tanggal', today())->where('status', 'aktif')->first();

        $antrean = collect();
        $sudahDiukur = collect();
        if ($activeSession) {
            // Get all Absensi today where Warga DOES NOT have Pemeriksaan for this session
            $antrean = Absensi::with('warga.balita', 'warga.ibuHamil')
                ->where('sesi_posyandu_id', $activeSession->id)
                ->whereDoesntHave('warga.pemeriksaans', function ($q) use ($activeSession) {
                    $q->where('sesi_posyandu_id', $activeSession->id);
                })
                ->get();

            // Get pemeriksaan that have been completed in this session
            $sudahDiukur = Pemeriksaan::with('warga.balita', 'warga.ibuHamil')
                ->where('sesi_posyandu_id', $activeSession->id)
                ->latest()
                ->get();
        }

        return view('kader.pengukuran.index', compact('activeSession', 'antrean', 'sudahDiukur'));
    }

    public function create(Warga $warga)
    {
        $activeSession = SesiPosyandu::whereDate('tanggal', today())->where('status', 'aktif')->first();
        if (! $activeSession) {
            return redirect()->route('kader.pengukuran.index')->with('error', 'Tidak ada sesi aktif.');
        }

        $isRegistered = Absensi::where('warga_id', $warga->id)->where('sesi_posyandu_id', $activeSession->id)->exists();
        if (!$isRegistered) {
            return redirect()->route('kader.pengukuran.index')->with('error', 'Warga tidak terdaftar pada sesi posyandu ini.');
        }

        $alreadyMeasured = Pemeriksaan::where('warga_id', $warga->id)->where('sesi_posyandu_id', $activeSession->id)->exists();
        if ($alreadyMeasured) {
            return redirect()->route('kader.pengukuran.index')->with('error', 'Warga sudah melakukan pengukuran pada sesi ini.');
        }

        return view('kader.pengukuran.create', compact('warga', 'activeSession'));
    }

    public function store(Request $request, Warga $warga)
    {
        $activeSession = SesiPosyandu::whereDate('tanggal', today())->where('status', 'aktif')->first();
        if (! $activeSession) {
            return redirect()->route('kader.pengukuran.index')->with('error', 'Sesi sudah berakhir.');
        }

        $isRegistered = Absensi::where('warga_id', $warga->id)->where('sesi_posyandu_id', $activeSession->id)->exists();
        if (!$isRegistered) {
            return redirect()->route('kader.pengukuran.index')->with('error', 'Warga tidak terdaftar pada sesi posyandu ini.');
        }

        $alreadyMeasured = Pemeriksaan::where('warga_id', $warga->id)->where('sesi_posyandu_id', $activeSession->id)->exists();
        if ($alreadyMeasured) {
            return redirect()->route('kader.pengukuran.index')->with('error', 'Warga sudah melakukan pengukuran pada sesi ini.');
        }

        // Auto-detect cohort type to adjust validation
        $isBalita = $warga->balita !== null;

        $validated = $request->validate([
            'berat_badan' => $isBalita ? 'required|numeric|min:0.1' : 'nullable|numeric',
            'tinggi_badan' => $isBalita ? 'required|numeric|min:10' : 'nullable|numeric',
            'lingkar_kepala' => 'nullable|numeric',
            'lingkar_lengan' => 'nullable|numeric',
            'tensi_darah' => 'nullable|string',
        ]);

        // Auto-detect cohort type from warga relationships
        $tipeKohort = null;
        if ($warga->balita) {
            $tipeKohort = 'balita';
        } elseif ($warga->ibuHamil) {
            $tipeKohort = 'ibu_hamil';
        } elseif ($warga->lansia) {
            $tipeKohort = 'lansia';
        }

        DB::beginTransaction();
        try {
            $pemeriksaan = Pemeriksaan::create([
                'warga_id' => $warga->id,
                'sesi_posyandu_id' => $activeSession->id,
                'tipe_kohort' => $tipeKohort,
                'berat_badan' => $validated['berat_badan'] ?? null,
                'tinggi_badan' => $validated['tinggi_badan'] ?? null,
                'posisi_ukur' => $request->input('posisi_ukur', 'terlentang'),
                'lingkar_kepala' => $validated['lingkar_kepala'] ?? null,
                'lingkar_lengan' => $validated['lingkar_lengan'] ?? null,
                'tensi_darah' => $validated['tensi_darah'] ?? null,
                'is_validated' => false,
                'petugas_id' => auth()->id(),
            ]);

            // Calculate and save Z-Score and EWS for Balita
            if ($tipeKohort === 'balita') {
                $wargaWithBalita = Warga::with(['balita'])->find($warga->id);
                if ($wargaWithBalita && $wargaWithBalita->balita && $pemeriksaan->berat_badan && $pemeriksaan->tinggi_badan) {
                    $dob = $wargaWithBalita->tanggal_lahir;
                    $sessionDate = $activeSession->tanggal;
                    $gender = $wargaWithBalita->jenis_kelamin;
                    $posisiUkur = $request->input('posisi_ukur', 'terlentang');

                    $zscoreService = app(WhoZscoreService::class);

                    $bbuRes = $zscoreService->calculateBBU($pemeriksaan->berat_badan, $dob, $sessionDate, $gender);
                    $tbuRes = $zscoreService->calculateTBU($pemeriksaan->tinggi_badan, $dob, $sessionDate, $gender, $posisiUkur);
                    $bbtbRes = $zscoreService->calculateBBTB($pemeriksaan->berat_badan, $pemeriksaan->tinggi_badan, $dob, $sessionDate, $gender, $posisiUkur);

                    $zBbu = $bbuRes['zscore'];
                    $zTbu = $tbuRes['zscore'];
                    $zBbTb = $bbtbRes['zscore'];

                    // EWS Evaluation
                    $ewsResult = $zscoreService->evaluateEWS($warga, $zBbu, $zTbu, $pemeriksaan->berat_badan);

                    KalkulasiZScore::updateOrCreate(
                        ['pemeriksaan_id' => $pemeriksaan->id],
                        [
                            'zscore_bb_u' => $zBbu,
                            'raw_zscore_bb_u' => $bbuRes['raw_zscore'] ?? null,
                            'flag_bb_u' => $bbuRes['flag'] ?? null,
                            'zscore_tb_u' => $zTbu,
                            'raw_zscore_tb_u' => $tbuRes['raw_zscore'] ?? null,
                            'flag_tb_u' => $tbuRes['flag'] ?? null,
                            'zscore_bb_tb' => $zBbTb,
                            'raw_zscore_bb_tb' => $bbtbRes['raw_zscore'] ?? null,
                            'flag_bb_tb' => $bbtbRes['flag'] ?? null,
                            'status_gizi' => $bbuRes['status'],
                            'status_stunting' => $tbuRes['status'],
                            'status_bb_tb' => $bbtbRes['status'],
                            'age_in_days' => $bbuRes['age_in_days'] ?? $tbuRes['age_in_days'],
                            'measurement_type' => $tbuRes['measurement_type'] ?? 'LENGTH',
                            'source_version' => 'who_anthro_v1.1.0',
                            'ews_status' => $ewsResult['status'],
                            'ews_message' => $ewsResult['message'],
                        ]
                    );

                    if (in_array($ewsResult['status'], ['risiko_tinggi', 'bahaya'])) {
                        $this->triggerKunjunganRumahEWS($warga, $ewsResult);
                    }
                }
            }

            DB::commit();
            return redirect()->route('kader.pengukuran.index')->with('success', 'Data pengukuran awal '.$warga->nama.' berhasil disimpan dan masuk ke antrean Bidan.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Kader Pengukuran Error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat memproses data pengukuran: ' . $e->getMessage());
        }
    }

    /**
     * Otomatis buat entri kunjungan rumah untuk Bidan berdasarkan EWS.
     */
    private function triggerKunjunganRumahEWS(Warga $warga, array $ewsResult): void
    {
        $existingVisit = KunjunganRumah::where('warga_id', $warga->id)
            ->whereIn('status', ['dijadwalkan', 'ditunda'])
            ->where('tipe', '2t')
            ->exists();

        if (! $existingVisit) {
            KunjunganRumah::create([
                'warga_id' => $warga->id,
                'tipe' => '2t',
                'prioritas' => $ewsResult['status'] === 'bahaya' ? 'sangat_tinggi' : 'tinggi',
                'tanggal_rencana' => now()->addDays(3)->toDateString(),
                'status' => 'dijadwalkan',
                'catatan_sebelum' => "⚠️ PERINGATAN OTOMATIS (EWS): {$ewsResult['message']} Segera lakukan kunjungan rumah untuk intervensi gizi.",
            ]);
        }
    }
}
