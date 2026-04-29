<?php

namespace App\Http\Controllers\Kader;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SesiPosyandu;
use App\Models\Absensi;
use App\Models\Warga;
use App\Models\Region;
use App\Models\Balita;
use App\Models\IbuHamil;
use App\Models\Lansia;
use Illuminate\Support\Facades\DB;

class PendaftaranController extends Controller
{
    public function index()
    {
        $activeSession = SesiPosyandu::whereDate('tanggal', today())->where('status', 'aktif')->first();
        $recentAbsensi = collect();
        
        if ($activeSession) {
            $recentAbsensi = Absensi::with('warga')->where('sesi_posyandu_id', $activeSession->id)->latest()->take(10)->get();
        }

        return view('kader.pendaftaran.scan', compact('activeSession', 'recentAbsensi'));
    }

    public function scan(Request $request)
    {
        $request->validate([
            'nik' => 'required|string',
            'sesi_id' => 'required|exists:sesi_posyandu,id',
        ]);

        $warga = Warga::where('nik', $request->nik)->first();
        if (!$warga) {
            return response()->json(['success' => false, 'message' => 'Data warga tidak ditemukan.']);
        }

        $exists = Absensi::where('warga_id', $warga->id)->where('sesi_posyandu_id', $request->sesi_id)->exists();
        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Warga ' . $warga->nama . ' sudah terdaftar pada sesi ini.']);
        }

        $absensi = Absensi::create([
            'warga_id' => $warga->id,
            'sesi_posyandu_id' => $request->sesi_id,
            'status' => 'hadir'
        ]);

        return response()->json(['success' => true, 'message' => 'Warga ' . $warga->nama . ' berhasil didaftarkan.', 'warga' => $warga]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nik' => 'required|string',
        ]);

        $warga = Warga::where('nik', $request->nik)->first();
        if (!$warga) {
            return back()->with('error', 'Warga dengan NIK tersebut tidak ditemukan.');
        }

        $activeSession = SesiPosyandu::whereDate('tanggal', today())->where('status', 'aktif')->first();
        if (!$activeSession) {
            return back()->with('error', 'Tidak ada Sesi Posyandu aktif hari ini.');
        }

        $exists = Absensi::where('warga_id', $warga->id)->where('sesi_posyandu_id', $activeSession->id)->exists();
        if ($exists) {
            return back()->with('error', 'Warga ' . $warga->nama . ' sudah terdaftar absen hari ini.');
        }

        Absensi::create([
            'warga_id' => $warga->id,
            'sesi_posyandu_id' => $activeSession->id,
            'status' => 'hadir'
        ]);

        return back()->with('success', 'Kehadiran ' . $warga->nama . ' berhasil dicatat.');
    }

}
