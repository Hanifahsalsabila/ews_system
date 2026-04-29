<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Real Enterprise Metrics
        $metrics = [
            'total_warga' => \App\Models\Warga::count(),
            'total_balita' => \App\Models\Balita::count(),
            'total_ibu_hamil' => \App\Models\IbuHamil::count(),
            'total_lansia' => \App\Models\Lansia::count(),
            'total_sesi' => \App\Models\SesiPosyandu::count(),
            'sesi_aktif' => \App\Models\SesiPosyandu::where('status', 'aktif')->count(),
        ];
        
        // Real Audit Trail Data
        $recent_activities = \OwenIt\Auditing\Models\Audit::with('user')
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
                $metrics['total_warga'] - ($metrics['total_balita'] + $metrics['total_ibu_hamil'] + $metrics['total_lansia'])
            ]
        ];

        return view('admin.dashboard.index', compact('metrics', 'recent_activities', 'chartData'));
    }
}
