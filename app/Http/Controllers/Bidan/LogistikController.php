<?php

namespace App\Http\Controllers\Bidan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vaksin;
use App\Models\Inventaris;

class LogistikController extends Controller
{
    public function index()
    {
        $vaksins = Vaksin::all();
        $inventaris = Inventaris::all();
        
        return view('bidan.logistik.index', compact('vaksins', 'inventaris'));
    }
}
