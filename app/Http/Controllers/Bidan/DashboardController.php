<?php

namespace App\Http\Controllers\Bidan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Balita;
use App\Models\IbuHamil;
use App\Models\Warga;
use App\Models\Absensi;
use App\Models\KalkulasiZscore;

class DashboardController extends Controller
{
    public function index()
    {
        $totalBalita = Balita::count();
        $totalIbuHamil = IbuHamil::count();
        
        $totalWarga = Warga::count();
        if ($totalWarga == 0) $totalWarga = 1; // Prevent division by zero
        
        $absensiBulanIni = Absensi::whereMonth('created_at', now()->month)
                            ->whereYear('created_at', now()->year)->count();
        $persentaseKehadiran = round(($absensiBulanIni / $totalWarga) * 100, 1);

        $angkaStunting = KalkulasiZscore::where('status_stunting', 'Stunting')
                            ->orWhere('status_gizi', 'BGM')
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
                                
            $stuntingData[] = KalkulasiZscore::where(function($q) {
                                    $q->where('status_stunting', 'Stunting')
                                      ->orWhere('status_gizi', 'BGM');
                                })
                                ->whereMonth('created_at', $month->month)
                                ->whereYear('created_at', $month->year)->count();
        }

        return view('bidan.dashboard', compact(
            'totalBalita', 'totalIbuHamil', 'persentaseKehadiran', 'angkaStunting',
            'chartLabels', 'attendanceData', 'stuntingData'
        ));
    }
}
