<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Warga;
use App\Models\SesiPosyandu;
use App\Models\Pemeriksaan;

class PelaporanController extends Controller
{
    public function index()
    {
        $stats = [
            'total_warga' => Warga::count(),
            'total_pemeriksaan' => Pemeriksaan::count(),
            'total_sesi' => SesiPosyandu::count(),
        ];

        return view('admin.laporan.index', compact('stats'));
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

        $fileName = 'Laporan_SIP_'.$request->format_id.'_'.$request->bulan.'_'.$request->tahun.'.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\SipExport($request->all()), 
            $fileName
        );
    }
}
