<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use App\Models\Warga;
use App\Models\Pemeriksaan;

class RiwayatController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $warga = Warga::where('user_id', $user->id)->first();

        if (!$warga) {
            return redirect()->route('warga.dashboard');
        }

        $pemeriksaans = Pemeriksaan::with('sesiPosyandu', 'petugas')
            ->where('warga_id', $warga->id)
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('warga.riwayat.index', compact('warga', 'pemeriksaans'));
    }
}
