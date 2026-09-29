<?php

namespace App\Http\Controllers;

use App\Models\Balita;
use App\Models\IbuHamil;
use App\Models\Lansia;
use App\Models\SesiPosyandu;
use App\Models\Warga;
use OwenIt\Auditing\Models\Audit;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Real Enterprise Metrics
        $metrics = [
            'total_warga' => Warga::count(),
            'total_balita' => Balita::count(),
            'total_ibu_hamil' => IbuHamil::count(),
            'total_lansia' => Lansia::count(),
            'total_sesi' => SesiPosyandu::count(),
            'sesi_aktif' => SesiPosyandu::where('status', 'aktif')->count(),
        ];

        // Real Audit Trail Data
        $recent_activities = Audit::with('user')
            ->latest()
            ->limit(7)
            ->get();

        // Chart Data: Kohort Distribution
        $chartData = [
            'labels' => ['Balita', 'Ibu Hamil', 'Lansia', 'Umum'],
            'data' => [
                $metrics['total_balita'],
                $metrics['total_ibu_hamil'],
                $metrics['total_lansia'],
                $metrics['total_warga'] - ($metrics['total_balita'] + $metrics['total_ibu_hamil'] + $metrics['total_lansia']),
            ],
        ];

        return view('admin.dashboard.index', compact('metrics', 'recent_activities', 'chartData'));
    }
}
