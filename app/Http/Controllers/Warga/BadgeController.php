<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use App\Models\Absensi;

class BadgeController extends Controller
{
    private array $badgeDefinitions = [
        ['name' => 'Ibu Rajin', 'icon' => '⭐', 'desc' => 'Hadir 3 bulan berturut-turut', 'streak' => 3],
        ['name' => 'Ibu Siaga', 'icon' => '🏆', 'desc' => 'Hadir 6 bulan berturut-turut', 'streak' => 6],
        ['name' => 'Ibu Teladan', 'icon' => '👑', 'desc' => 'Hadir 12 bulan berturut-turut', 'streak' => 12],
    ];

    public function index()
    {
        $user = auth()->user();
        $this->awardBadges($user);

        $badges = $user->fresh()->badges ?? [];
        $earnedNames = array_column($badges, 'name');

        return view('warga.badges.index', [
            'badges' => $badges,
            'allBadges' => $this->badgeDefinitions,
            'earnedNames' => $earnedNames,
        ]);
    }

    private function awardBadges($user): void
    {
        $warga = $user->warga;
        if (! $warga) {
            return;
        }

        $attendedMonths = Absensi::where('warga_id', $warga->id)
            ->where('status', 'hadir')
            ->join('sesi_posyandus', 'absensi.sesi_posyandu_id', '=', 'sesi_posyandus.id')
            ->selectRaw("DATE_FORMAT(sesi_posyandus.tanggal, '%Y-%m') as bulan")
            ->distinct()
            ->orderBy('bulan')
            ->pluck('bulan')
            ->toArray();

        $streak = $this->calculateConsecutiveStreak($attendedMonths);
        $currentBadges = $user->badges ?? [];

        foreach ($this->badgeDefinitions as $def) {
            $alreadyEarned = collect($currentBadges)->first(fn ($b) => is_array($b) && ($b['name'] ?? null) === $def['name']
            );

            if (! $alreadyEarned && $streak >= $def['streak']) {
                $currentBadges[] = [
                    'name' => $def['name'],
                    'icon' => $def['icon'],
                    'desc' => $def['desc'],
                    'earned_at' => now()->toDateTimeString(),
                ];
            }
        }

        if ($currentBadges !== ($user->badges ?? [])) {
            $user->update(['badges' => $currentBadges]);
        }
    }

    private function calculateConsecutiveStreak(array $months): int
    {
        if (empty($months)) {
            return 0;
        }

        $streak = 1;
        $maxStreak = 1;

        for ($i = 1; $i < count($months); $i++) {
            $prev = new \DateTime($months[$i - 1].'-01');
            $curr = new \DateTime($months[$i].'-01');
            $diff = (int) $prev->diff($curr)->m + ((int) $prev->diff($curr)->y * 12);

            if ($diff === 1) {
                $streak++;
                $maxStreak = max($maxStreak, $streak);
            } else {
                $streak = 1;
            }
        }

        return $maxStreak;
    }
}
