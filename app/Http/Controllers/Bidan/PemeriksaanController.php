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
            $periksaHariIni = Pemeriksaan::where('sesi_posyandu_id', $sesiAktif->id)->pluck('warga_id');

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

        // Get list of warga_ids already examined in this session
        $sudahDiperiksa = collect();
        if ($sesiAktif) {
            $sudahDiperiksa = Pemeriksaan::where('sesi_posyandu_id', $sesiAktif->id)
                ->where('tipe_kohort', 'ibu_hamil')
                ->pluck('warga_id');
        }

        return view('bidan.pemeriksaan.ibu_hamil', compact('ibuHamil', 'sesiAktif', 'sudahDiperiksa'));
    }

    public function balita(Request $request)
    {
        $sesiAktif = SesiPosyandu::whereDate('tanggal', today())->where('status', 'aktif')->first();

        $balita = Balita::with(['warga.pemeriksaans' => function ($q) {
            $q->where('tipe_kohort', 'balita')->latest()->take(3);
        }])->get();

        $sudahDiperiksa = collect();
        if ($sesiAktif) {
            $sudahDiperiksa = Pemeriksaan::where('sesi_posyandu_id', $sesiAktif->id)
                ->where('tipe_kohort', 'balita')
                ->pluck('warga_id');
        }

        return view('bidan.pemeriksaan.balita', compact('balita', 'sesiAktif', 'sudahDiperiksa'));
    }

    public function lansia(Request $request)
    {
        $sesiAktif = SesiPosyandu::whereDate('tanggal', today())->where('status', 'aktif')->first();

        $lansia = Lansia::with(['warga.pemeriksaans' => function ($q) {
            $q->where('tipe_kohort', 'lansia')->latest()->take(3);
        }])->get();

        $sudahDiperiksa = collect();
        if ($sesiAktif) {
            $sudahDiperiksa = Pemeriksaan::where('sesi_posyandu_id', $sesiAktif->id)
                ->where('tipe_kohort', 'lansia')
                ->pluck('warga_id');
        }

        return view('bidan.pemeriksaan.lansia', compact('lansia', 'sesiAktif', 'sudahDiperiksa'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'warga_id' => 'required|exists:warga,id',
            'sesi_posyandu_id' => 'required|exists:sesi_posyandu,id',
            'tipe_kohort' => 'required|in:balita,ibu_hamil,lansia',
        ]);

        // Prevent duplicate examination in the same session for the same cohort type
        $exists = Pemeriksaan::where('warga_id', $request->warga_id)
            ->where('sesi_posyandu_id', $request->sesi_posyandu_id)
            ->where('tipe_kohort', $request->tipe_kohort)
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Warga ini sudah diperiksa pada sesi ini.');
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

        Pemeriksaan::create([
            'warga_id' => $request->warga_id,
            'sesi_posyandu_id' => $request->sesi_posyandu_id,
            'tipe_kohort' => $request->tipe_kohort,
            'berat_badan' => $request->berat_badan,
            'tinggi_badan' => $request->tinggi_badan,
            'lingkar_kepala' => $request->lingkar_kepala,
            'lingkar_lengan' => $request->lingkar_lengan,
            'tfu' => $request->tfu,
            'djj' => $request->djj,
            'tensi_darah' => $request->tensi_darah,
            'keluhan' => $request->keluhan,
            'tindakan' => $tindakan,
            'catatan' => $request->catatan,
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
        ]);

        return redirect()->back()->with('success', 'Data pemeriksaan berhasil disimpan!');
    }
}
