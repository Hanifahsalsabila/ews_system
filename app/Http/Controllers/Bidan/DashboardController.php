<?php

namespace App\Http\Controllers\Bidan;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Balita;
use App\Models\IbuHamil;
use App\Models\KalkulasiZscore;
use App\Models\Lansia;
use App\Models\Warga;

class DashboardController extends Controller
{
    public function index()
    {
        $totalBalita = Balita::count();
        $totalIbuHamil = IbuHamil::count();
        $totalLansia = Lansia::count();

        $totalWarga = Warga::count();
        if ($totalWarga == 0) {
            $totalWarga = 1;
        } // Prevent division by zero

        $absensiBulanIni = Absensi::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)->count();
        $persentaseKehadiran = round(($absensiBulanIni / $totalWarga) * 100, 1);

        $stuntingStatuses = ['Pendek (Stunted)', 'Sangat Pendek (Severely Stunted)', 'Stunting'];
        $bgmStatuses = ['Sangat Kurang (Severely Underweight)', 'BGM'];

        $angkaStunting = KalkulasiZscore::whereIn('status_stunting', $stuntingStatuses)
            ->orWhereIn('status_gizi', $bgmStatuses)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)->count();

        // 6 Months Chart Data
        $chartLabels = [];
        $attendanceData = [];
        $stuntingData = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $chartLabels[] = $month->translatedFormat('M Y');

            $attendanceData[] = Absensi::whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)->count();

            $stuntingData[] = KalkulasiZscore::where(function ($q) use ($stuntingStatuses, $bgmStatuses) {
                $q->whereIn('status_stunting', $stuntingStatuses)
                    ->orWhereIn('status_gizi', $bgmStatuses);
            })
                ->whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)->count();
        }

        // EWS Alerts
        $ewsAlerts = KalkulasiZscore::with('pemeriksaan.warga')
            ->whereIn('ews_status', ['waspada', 'risiko_tinggi', 'bahaya'])
            ->orderByRaw("FIELD(ews_status, 'bahaya', 'risiko_tinggi', 'waspada')")
            ->orderByDesc('created_at')
            ->take(10)
            ->get();

        return view('bidan.dashboard', compact(
            'totalBalita', 'totalIbuHamil', 'totalLansia', 'persentaseKehadiran', 'angkaStunting',
            'chartLabels', 'attendanceData', 'stuntingData', 'ewsAlerts'
        ));
    }
}
