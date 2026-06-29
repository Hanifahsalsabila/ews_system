<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use App\Models\Warga;
use App\Models\Pemeriksaan;
use Illuminate\Http\Request;

class RiwayatController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Get all Warga records associated with this parent
        $wargas = Warga::where('user_id', $user->id)->get();
        
        if ($wargas->isEmpty()) {
            return redirect()->route('warga.dashboard')->with('error', 'Tidak ada data warga terkait akun ini.');
        }

        // Allow filtering by specific child if there are multiple
        $selectedWargaId = $request->query('warga_id', $wargas->first()->id);
        $warga = $wargas->where('id', $selectedWargaId)->first() ?? $wargas->first();

        // Eager load data required by the kms view
        $warga->load(['balita', 'ibuHamil', 'lansia', 'pemeriksaans' => function($q) {
            $q->orderBy('created_at', 'asc'); // Dari awal ke akhir untuk grafik
        }]);

        return view('warga.kms', compact('warga', 'wargas'));
    }
}
