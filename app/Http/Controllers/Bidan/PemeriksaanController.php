<?php

namespace App\Http\Controllers\Bidan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pemeriksaan;
use App\Models\SesiPosyandu;
use App\Models\IbuHamil;
use App\Models\Balita;

class PemeriksaanController extends Controller
{
    public function index()
    {
        // View to choose which one to check
        $sesiAktif = SesiPosyandu::whereDate('tanggal', today())->where('status', 'aktif')->first();
        return view('bidan.pemeriksaan.index', compact('sesiAktif'));
    }

    public function ibuHamil(Request $request)
    {
        $sesiAktif = SesiPosyandu::whereDate('tanggal', today())->where('status', 'aktif')->first();
        
        $ibuHamil = IbuHamil::with('warga')->get();
        return view('bidan.pemeriksaan.ibu_hamil', compact('ibuHamil', 'sesiAktif'));
    }

    public function balita(Request $request)
    {
        $sesiAktif = SesiPosyandu::whereDate('tanggal', today())->where('status', 'aktif')->first();
        
        $balita = Balita::with('warga')->get();
        return view('bidan.pemeriksaan.balita', compact('balita', 'sesiAktif'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'warga_id' => 'required|exists:wargas,id',
            'sesi_posyandu_id' => 'required|exists:sesi_posyandu,id',
        ]);

        $tindakan = $request->tindakan;
        
        if ($request->has('imunisasi')) {
            $imunisasiStr = implode(', ', $request->imunisasi);
            $tindakan = $tindakan ? $tindakan . ' | Imunisasi: ' . $imunisasiStr : 'Imunisasi: ' . $imunisasiStr;
        }

        Pemeriksaan::create([
            'warga_id' => $request->warga_id,
            'sesi_posyandu_id' => $request->sesi_posyandu_id,
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
            'is_validated' => true, // Bidan is validator
            'petugas_id' => auth()->id()
        ]);

        return redirect()->back()->with('success', 'Data pemeriksaan berhasil disimpan!');
    }
}
