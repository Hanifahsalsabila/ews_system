<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole(['Super_Admin'])) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasRole(['Puskesmas'])) {
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
