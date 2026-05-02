<?php

namespace App\Http\Controllers\Kader;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Warga;
use App\Models\Region;
use App\Models\Balita;
use App\Models\IbuHamil;
use App\Models\Lansia;
use Illuminate\Support\Facades\DB;

class WargaController extends Controller
{
    public function index(Request $request)
    {
        // Only load the hierarchy to display correctly
        $query = Warga::with(['region.parent.parent', 'balita', 'ibuHamil', 'lansia']);

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $wargas = $query->orderBy('nama')->paginate(15);
        // Note: No need to pass regions if we aren't creating/editing
        
        return view('kader.warga.index', compact('wargas'));
    }
}
