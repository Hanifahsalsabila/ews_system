<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;

class BadgeController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $badges = $user->badges ?? [];

        // Daftar semua badge yang bisa didapatkan
        $allBadges = [
            ['name' => 'Ibu Rajin', 'icon' => '⭐', 'desc' => 'Hadir 3 bulan berturut-turut', 'streak' => 3],
            ['name' => 'Ibu Siaga', 'icon' => '🏆', 'desc' => 'Hadir 6 bulan berturut-turut', 'streak' => 6],
            ['name' => 'Ibu Teladan', 'icon' => '👑', 'desc' => 'Hadir 12 bulan berturut-turut', 'streak' => 12],
        ];

        $earnedNames = array_column($badges, 'name');

        return view('warga.badges.index', compact('badges', 'allBadges', 'earnedNames'));
    }
}
