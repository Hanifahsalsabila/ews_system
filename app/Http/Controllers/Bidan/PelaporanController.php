<?php

namespace App\Http\Controllers\Bidan;

use App\Exports\SipExport;
use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Balita;
use App\Models\IbuHamil;
use App\Models\Pemeriksaan;
use App\Models\Warga;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PelaporanController extends Controller
{
    public function index()
    {
        // Visualisasi Cakupan
        $totalBalita = Balita::count();
        $totalIbuHamil = IbuHamil::count();

        $absensiBulanIni = Absensi::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)->count();

        // Menghitung jumlah unik warga yang periksa bulan ini (Balita & Ibu hamil)
        $pemeriksaanBulanIni = Pemeriksaan::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->distinct('warga_id')
            ->count();

        $targetLayanan = $totalBalita + $totalIbuHamil;
        $cakupanLayanan = $targetLayanan > 0 ? round(($pemeriksaanBulanIni / $targetLayanan) * 100, 1) : 0;

        return view('bidan.pelaporan.index', compact(
            'totalBalita', 'totalIbuHamil', 'pemeriksaanBulanIni', 'targetLayanan', 'cakupanLayanan'
        ));
    }

    public function export(Request $request)
    {
        $request->validate([
            'format_id' => 'required|integer|between:1,6',
            'bulan' => 'required|string|size:2',
            'tahun' => 'required|string|size:4',
            'kepala_desa' => 'nullable|string',
            'ketua_kader' => 'nullable|string',
        ]);

        $fileName = 'Laporan_Bulanan_Bidan_'.$request->format_id.'_'.$request->bulan.'_'.$request->tahun.'.xlsx';

        return Excel::download(
            new SipExport($request->all()),
            $fileName
        );
    }
}
