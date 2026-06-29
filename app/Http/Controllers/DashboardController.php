<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole(['Super_Admin', 'Admin'])) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasRole(['Bidan_Desa', 'Puskesmas'])) {
            return redirect()->route('bidan.dashboard');
        }

        if ($user->hasRole('Kader_Posyandu')) {
            return redirect()->route('kader.dashboard');
        }

        if ($user->hasRole('orang_tua')) {
            return redirect()->route('warga.dashboard');
        }

        // Default fallback
        return view('dashboard');
    }
}
