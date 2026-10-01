<?php

namespace App\Http\Controllers\Bidan;

use App\Http\Controllers\Controller;
use App\Models\Balita;
use App\Models\IbuHamil;
use App\Models\KalkulasiZScore;
use App\Models\Lansia;
use App\Models\Pemeriksaan;
use App\Models\SesiPosyandu;
use App\Models\Warga;
use App\Services\WhoZscoreService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PemeriksaanController extends Controller
{
    public function index()
    {
        $sesiAktif = SesiPosyandu::whereDate('tanggal', today())->where('status', 'aktif')->first();

        // Count cohort totals
        $totalBalita = Balita::count();
        $totalIbuHamil = IbuHamil::count();
        $totalLansia = Lansia::count();

        // Count examined today
        $sudahDiperiksaBalita = 0;
        $sudahDiperiksaIbuHamil = 0;
        $sudahDiperiksaLansia = 0;

        if ($sesiAktif) {
            $periksaHariIni = Pemeriksaan::where('sesi_posyandu_id', $sesiAktif->id)
                ->where('is_validated', true)
                ->pluck('warga_id');

            $sudahDiperiksaBalita = Balita::whereIn('warga_id', $periksaHariIni)->count();
            $sudahDiperiksaIbuHamil = IbuHamil::whereIn('warga_id', $periksaHariIni)->count();
            $sudahDiperiksaLansia = Lansia::whereIn('warga_id', $periksaHariIni)->count();
        }

        return view('bidan.pemeriksaan.index', compact(
            'sesiAktif',
            'totalBalita', 'totalIbuHamil', 'totalLansia',
            'sudahDiperiksaBalita', 'sudahDiperiksaIbuHamil', 'sudahDiperiksaLansia'
        ));
    }

    public function ibuHamil(Request $request)
    {
        $sesiAktif = SesiPosyandu::whereDate('tanggal', today())->where('status', 'aktif')->first();

        $ibuHamil = IbuHamil::with(['warga.pemeriksaans' => function ($q) {
            $q->where('tipe_kohort', 'ibu_hamil')->latest()->take(3);
        }])->get();

        // Get list of warga_ids already examined today
        $sudahDiperiksa = Pemeriksaan::whereDate('created_at', today())
            ->where('tipe_kohort', 'ibu_hamil')
            ->where('is_validated', true)
            ->pluck('warga_id');

        return view('bidan.pemeriksaan.ibu_hamil', compact('ibuHamil', 'sesiAktif', 'sudahDiperiksa'));
    }

    public function balita(Request $request)
    {
        $sesiAktif = SesiPosyandu::whereDate('tanggal', today())->where('status', 'aktif')->first();

        $balita = Balita::with(['warga.pemeriksaans' => function ($q) {
            $q->where('tipe_kohort', 'balita')->latest()->take(3);
        }])->get();

        $sudahDiperiksa = Pemeriksaan::whereDate('created_at', today())
            ->where('tipe_kohort', 'balita')
            ->where('is_validated', true)
            ->pluck('warga_id');

        return view('bidan.pemeriksaan.balita', compact('balita', 'sesiAktif', 'sudahDiperiksa'));
    }

    public function lansia(Request $request)
    {
        $sesiAktif = SesiPosyandu::whereDate('tanggal', today())->where('status', 'aktif')->first();

        $lansia = Lansia::with(['warga.pemeriksaans' => function ($q) {
            $q->where('tipe_kohort', 'lansia')->latest()->take(3);
        }])->get();

        $sudahDiperiksa = Pemeriksaan::whereDate('created_at', today())
            ->where('tipe_kohort', 'lansia')
            ->where('is_validated', true)
            ->pluck('warga_id');

        return view('bidan.pemeriksaan.lansia', compact('lansia', 'sesiAktif', 'sudahDiperiksa'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'warga_id' => 'required|exists:warga,id',
            'sesi_posyandu_id' => 'required|exists:sesi_posyandu,id',
            'tipe_kohort' => 'required|in:balita,ibu_hamil,lansia',
        ]);

        // Check if there's an existing record
        $pemeriksaan = Pemeriksaan::where('warga_id', $request->warga_id)
            ->where('sesi_posyandu_id', $request->sesi_posyandu_id)
            ->where('tipe_kohort', $request->tipe_kohort)
            ->first();

        if ($pemeriksaan && $pemeriksaan->is_validated) {
            return redirect()->back()->with('error', 'Warga ini sudah divalidasi pada sesi ini.');
        }

        $tindakan = $request->tindakan;

        if ($request->has('imunisasi')) {
            $imunisasiStr = implode(', ', $request->imunisasi);
            $tindakan = $tindakan ? $tindakan.' | Imunisasi: '.$imunisasiStr : 'Imunisasi: '.$imunisasiStr;
        }

        // Calculate IMT if both weight and height are provided
        $imt = null;
        if ($request->berat_badan && $request->tinggi_badan) {
            $tinggiM = $request->tinggi_badan / 100;
            if ($tinggiM > 0) {
                $imt = round($request->berat_badan / ($tinggiM * $tinggiM), 2);
            }
        }

        $data = [
            'berat_badan' => $request->berat_badan ?? ($pemeriksaan->berat_badan ?? null),
            'tinggi_badan' => $request->tinggi_badan ?? ($pemeriksaan->tinggi_badan ?? null),
            'posisi_ukur' => $request->input('posisi_ukur', 'terlentang'),
            'lingkar_kepala' => $request->lingkar_kepala ?? ($pemeriksaan->lingkar_kepala ?? null),
            'lingkar_lengan' => $request->lingkar_lengan ?? ($pemeriksaan->lingkar_lengan ?? null),
            'tfu' => $request->tfu ?? ($pemeriksaan->tfu ?? null),
            'djj' => $request->djj ?? ($pemeriksaan->djj ?? null),
            'tensi_darah' => $request->tensi_darah ?? ($pemeriksaan->tensi_darah ?? null),
            'keluhan' => $request->keluhan ?? ($pemeriksaan->keluhan ?? null),
            'tindakan' => $tindakan ?? ($pemeriksaan->tindakan ?? null),
            'catatan' => $request->catatan ?? ($pemeriksaan->catatan ?? null),
            // Balita fields
            'vitamin_a' => $request->boolean('vitamin_a'),
            'obat_cacing' => $request->boolean('obat_cacing'),
            'asi_eksklusif' => $request->boolean('asi_eksklusif'),
            'pmt' => $request->boolean('pmt'),
            // Ibu Hamil fields
            'suplementasi_fe' => $request->boolean('suplementasi_fe'),
            'status_tt' => $request->status_tt,
            // Lansia fields
            'gula_darah' => $request->gula_darah,
            'kolesterol' => $request->kolesterol,
            'asam_urat' => $request->asam_urat,
            'imt' => $imt,
            // Validation
            'is_validated' => true,
            'petugas_id' => auth()->id(),
        ];

        DB::beginTransaction();
        try {
            if ($pemeriksaan) {
                $pemeriksaan->update($data);
            } else {
                $data['warga_id'] = $request->warga_id;
                $data['sesi_posyandu_id'] = $request->sesi_posyandu_id;
                $data['tipe_kohort'] = $request->tipe_kohort;
                $pemeriksaan = Pemeriksaan::create($data);
            }

            // Calculate and save Z-Score and EWS for Balita
            if ($request->tipe_kohort === 'balita') {
                $warga = Warga::with(['balita'])->find($request->warga_id);
                $sesi = SesiPosyandu::find($request->sesi_posyandu_id);

                if ($warga && $warga->balita && $sesi && $pemeriksaan->berat_badan && $pemeriksaan->tinggi_badan) {
                    $dob = $warga->tanggal_lahir;
                    $sessionDate = $sesi->tanggal;
                    $gender = $warga->jenis_kelamin;
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
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Data pemeriksaan berhasil disimpan!');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Bidan Pemeriksaan Error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan pemeriksaan: ' . $e->getMessage());
        }
    }
}
