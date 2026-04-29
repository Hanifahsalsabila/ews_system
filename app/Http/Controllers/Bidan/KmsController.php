<?php

namespace App\Http\Controllers\Bidan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Balita;
use App\Models\Pemeriksaan;
use App\Models\KalkulasiZScore;

class KmsController extends Controller
{
    public function index(Request $request)
    {
        $balitas = Balita::with('warga')->get();
        $selectedBalita = null;
        $chartData = [];
        $riwayatPemeriksaan = [];

        if ($request->has('balita_id')) {
            $selectedBalita = Balita::with('warga')->where('warga_id', $request->balita_id)->first();
            
            if ($selectedBalita) {
                $riwayatPemeriksaan = Pemeriksaan::with('sesiPosyandu')
                    ->leftJoin('kalkulasi_zscore', 'pemeriksaan.id', '=', 'kalkulasi_zscore.pemeriksaan_id')
                    ->where('warga_id', $selectedBalita->warga_id)
                    ->orderBy('pemeriksaan.created_at', 'asc')
                    ->select('pemeriksaan.*', 'kalkulasi_zscore.status_stunting', 'kalkulasi_zscore.status_gizi', 'kalkulasi_zscore.zscore_bb_u')
                    ->get();

                $labels = [];
                $beratBadan = [];
                $zScoreBBU = [];

                foreach ($riwayatPemeriksaan as $p) {
                    $labels[] = $p->created_at->format('M Y');
                    $beratBadan[] = $p->berat_badan;
                    $zScoreBBU[] = $p->zscore_bb_u ?? 0;
                }

                $chartData = [
                    'labels' => $labels,
                    'beratBadan' => $beratBadan,
                    'zScoreBBU' => $zScoreBBU
                ];
            }
        }

        return view('bidan.kms.index', compact('balitas', 'selectedBalita', 'riwayatPemeriksaan', 'chartData'));
    }
}
