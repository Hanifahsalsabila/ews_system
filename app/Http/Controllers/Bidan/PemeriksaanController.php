<?php

namespace App\Http\Controllers\Bidan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pemeriksaan;
use App\Models\SesiPosyandu;
use App\Models\IbuHamil;
use App\Models\Balita;
use App\Models\Lansia;
use App\Models\Warga;

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
            $tindakan = $tindakan ? $tindakan . ' | Imunisasi: ' . $imunisasiStr : 'Imunisasi: ' . $imunisasiStr;
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
            'petugas_id' => auth()->id()
        ];

        if ($pemeriksaan) {
            $pemeriksaan->update($data);
        } else {
            $data['warga_id'] = $request->warga_id;
            $data['sesi_posyandu_id'] = $request->sesi_posyandu_id;
            $data['tipe_kohort'] = $request->tipe_kohort;
            $pemeriksaan = Pemeriksaan::create($data);
        }

        // Calculate and save Z-Score for Balita
        if ($request->tipe_kohort === 'balita') {
            $warga = \App\Models\Warga::with(['balita'])->find($request->warga_id);
            $sesi = \App\Models\SesiPosyandu::find($request->sesi_posyandu_id);

            if ($warga && $warga->balita && $sesi && $pemeriksaan->berat_badan && $pemeriksaan->tinggi_badan) {
                $dob = \Carbon\Carbon::parse($warga->tanggal_lahir);
                $sessionDate = \Carbon\Carbon::parse($sesi->tanggal);
                $ageInMonths = (int) $dob->diffInMonths($sessionDate);
                $gender = $warga->jenis_kelamin;

                $zscoreService = new \App\Services\WhoZscoreService();
                
                $zBbu = $zscoreService->calculate('BBU', $pemeriksaan->berat_badan, $ageInMonths, $gender);
                $zTbu = $zscoreService->calculate('TBU', $pemeriksaan->tinggi_badan, $ageInMonths, $gender);
                
                \App\Models\KalkulasiZScore::updateOrCreate(
                    ['pemeriksaan_id' => $pemeriksaan->id],
                    [
                        'zscore_bb_u' => $zBbu,
                        'zscore_tb_u' => $zTbu,
                        'status_gizi' => $zscoreService->getStatusBBU($zBbu),
                        'status_stunting' => $zscoreService->getStatusTBU($zTbu),
                    ]
                );
            }
        }

        return redirect()->back()->with('success', 'Data pemeriksaan berhasil disimpan!');
    }
}
