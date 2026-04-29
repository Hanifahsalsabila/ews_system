<?php

namespace App\Http\Controllers\Bidan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Warga;
use App\Models\Balita;
use App\Models\IbuHamil;
use App\Models\SesiPosyandu;

class NotifikasiController extends Controller
{
    public function index()
    {
        // Get Ibu Hamil and Balita with Warga relationship to get their phone numbers
        // In this simple implementation, we assume Warga has a 'no_hp' field.
        // Let's get all Balita and Ibu Hamil
        $balitas = Balita::with('warga')->get();
        $ibuHamils = IbuHamil::with('warga')->get();
        
        $sesiMendatang = SesiPosyandu::where('tanggal', '>=', now()->toDateString())
                                     ->where('status', 'Terjadwal')
                                     ->orderBy('tanggal', 'asc')
                                     ->first();

        return view('bidan.notifikasi.index', compact('balitas', 'ibuHamils', 'sesiMendatang'));
    }
}
