<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Warga;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Find the specific Warga associated with this user
        // We use get() just in case there are multiple, though usually it's one.
        $wargas = Warga::with(['balita', 'ibuHamil', 'lansia', 'pemeriksaans'])->where('user_id', $user->id)->get();
        
        return view('warga.dashboard', compact('user', 'wargas'));
    }

    public function kms(Warga $warga)
    {
        // Ensure this warga belongs to the logged in user
        if ($warga->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke data ini.');
        }

        $warga->load([
            'pemeriksaans' => function($q) { $q->orderBy('created_at', 'asc'); }
        ]);

        return view('warga.kms', compact('warga'));
    }
}
