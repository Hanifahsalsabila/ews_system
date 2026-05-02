<?php

namespace App\Http\Controllers\Warga;

use App\Http\Controllers\Controller;
use App\Models\Warga;
use App\Models\Pemeriksaan;
use App\Models\KalkulasiZScore;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $warga = Warga::where('user_id', $user->id)->first();

        if (!$warga) {
            return view('warga.dashboard.no-link');
        }

        // Ambil data balita, ibu hamil, lansia
        $warga->load('balita', 'ibuHamil', 'lansia', 'region');

        // Riwayat pemeriksaan (6 bulan terakhir)
        $pemeriksaans = Pemeriksaan::with('sesiPosyandu')
            ->where('warga_id', $warga->id)
            ->orderByDesc('created_at')
            ->take(12)
            ->get();

        // Data untuk grafik KMS (Kurva Pertumbuhan)
        $kmsData = $this->buildKmsData($warga, $pemeriksaans);

        // Riwayat imunisasi (dari field tindakan pada pemeriksaan)
        $imunisasiLog = $pemeriksaans->filter(fn($p) => !empty($p->tindakan))->values();

        // Hitung kehadiran berturut-turut (untuk badge/gamifikasi)
        $streakCount = $this->calculateAttendanceStreak($warga);

        // Badge yang sudah didapatkan
        $badges = $user->badges ?? [];

        // Cek apakah layak dapat badge baru
        $newBadge = $this->checkNewBadge($user, $streakCount);

        // Rekomendasi MPASI cerdas
        $mpasi = $this->getMpasiRecommendation($warga, $pemeriksaans);

        // Z-Score terakhir
        $latestZScore = null;
        if ($pemeriksaans->isNotEmpty()) {
            $latestZScore = KalkulasiZScore::where('pemeriksaan_id', $pemeriksaans->first()->id)->first();
        }

        return view('warga.dashboard.index', compact(
            'warga', 'pemeriksaans', 'kmsData', 'imunisasiLog',
            'streakCount', 'badges', 'newBadge', 'mpasi', 'latestZScore'
        ));
    }

    /**
     * Bangun data grafik KMS (BB/U per bulan).
     */
    private function buildKmsData(Warga $warga, $pemeriksaans): array
    {
        $labels = [];
        $bbData = [];
        $tbData = [];

        $sorted = $pemeriksaans->sortBy('created_at');

        foreach ($sorted as $p) {
            $labels[] = Carbon::parse($p->created_at)->translatedFormat('M Y');
            $bbData[] = $p->berat_badan;
            $tbData[] = $p->tinggi_badan;
        }

        return [
            'labels' => $labels,
            'bb' => $bbData,
            'tb' => $tbData,
        ];
    }

    /**
     * Hitung berapa bulan berturut-turut warga hadir di Posyandu.
     */
    private function calculateAttendanceStreak(Warga $warga): int
    {
        $pemeriksaans = Pemeriksaan::where('warga_id', $warga->id)
            ->orderByDesc('created_at')
            ->get();

        if ($pemeriksaans->isEmpty()) {
            return 0;
        }

        $streak = 0;
        $currentMonth = now()->startOfMonth();

        foreach ($pemeriksaans as $p) {
            $pMonth = Carbon::parse($p->created_at)->startOfMonth();
            $expectedMonth = $currentMonth->copy()->subMonths($streak);

            if ($pMonth->equalTo($expectedMonth)) {
                $streak++;
            } else {
                break;
            }
        }

        return $streak;
    }

    /**
     * Cek apakah user berhak mendapat badge baru.
     */
    private function checkNewBadge($user, int $streakCount): ?array
    {
        $badges = $user->badges ?? [];
        $badgeNames = array_column($badges, 'name');
        $newBadge = null;

        $badgeRules = [
            ['streak' => 3, 'name' => 'Ibu Rajin', 'icon' => '⭐', 'desc' => 'Hadir 3 bulan berturut-turut!'],
            ['streak' => 6, 'name' => 'Ibu Siaga', 'icon' => '🏆', 'desc' => 'Hadir 6 bulan berturut-turut!'],
            ['streak' => 12, 'name' => 'Ibu Teladan', 'icon' => '👑', 'desc' => 'Hadir 12 bulan berturut-turut! Luar biasa!'],
        ];

        foreach ($badgeRules as $rule) {
            if ($streakCount >= $rule['streak'] && !in_array($rule['name'], $badgeNames)) {
                $newBadge = $rule;
                $badges[] = [
                    'name' => $rule['name'],
                    'icon' => $rule['icon'],
                    'desc' => $rule['desc'],
                    'earned_at' => now()->toDateTimeString(),
                ];
                $user->update(['badges' => $badges]);
                break;
            }
        }

        return $newBadge;
    }

    /**
     * Rekomendasi MPASI cerdas berdasarkan usia dan status gizi.
     */
    private function getMpasiRecommendation(Warga $warga, $pemeriksaans): ?array
    {
        if (!$warga->balita) {
            return null;
        }

        $umurBulan = $warga->balita->umur_bulan;

        // Tentukan kategori usia MPASI
        if ($umurBulan < 6) {
            return [
                'kategori' => 'ASI Eksklusif',
                'pesan' => 'Bayi di bawah 6 bulan disarankan ASI eksklusif tanpa tambahan makanan apapun.',
                'resep' => [],
            ];
        }

        // Cek status gizi dari Z-Score terakhir
        $statusGizi = 'normal';
        if ($pemeriksaans->isNotEmpty()) {
            $latestZScore = KalkulasiZScore::where('pemeriksaan_id', $pemeriksaans->first()->id)->first();
            if ($latestZScore) {
                $statusGizi = strtolower($latestZScore->status_gizi ?? 'normal');
            }
        }

        $resepDb = $this->getMpasiRecipes($umurBulan, $statusGizi);

        return [
            'kategori' => $this->getMpasiAgeCategory($umurBulan),
            'pesan' => $this->getMpasiMessage($umurBulan, $statusGizi),
            'resep' => $resepDb,
        ];
    }

    private function getMpasiAgeCategory(int $umur): string
    {
        return match (true) {
            $umur >= 6 && $umur <= 8 => 'MPASI 6-8 Bulan (Tekstur Halus/Puree)',
            $umur >= 9 && $umur <= 11 => 'MPASI 9-11 Bulan (Tekstur Cincang Halus)',
            $umur >= 12 && $umur <= 23 => 'MPASI 12-23 Bulan (Makanan Keluarga)',
            default => 'Makanan Anak Balita',
        };
    }

    private function getMpasiMessage(int $umur, string $statusGizi): string
    {
        if (in_array($statusGizi, ['gizi_buruk', 'gizi_kurang', 'severely_underweight', 'underweight'])) {
            return '⚠️ Status gizi anak kurang optimal. Berikan MPASI tinggi kalori dan protein. Konsultasikan dengan Bidan/Petugas Gizi.';
        }

        return '✅ Pertumbuhan anak baik! Tetap berikan MPASI bergizi seimbang sesuai usia.';
    }

    private function getMpasiRecipes(int $umur, string $statusGizi): array
    {
        $needsExtraProtein = in_array($statusGizi, ['gizi_buruk', 'gizi_kurang', 'severely_underweight', 'underweight']);

        if ($umur >= 6 && $umur <= 8) {
            $recipes = [
                ['nama' => 'Puree Alpukat + ASI', 'bahan' => 'Alpukat matang, ASI', 'kalori' => '80 kkal'],
                ['nama' => 'Bubur Beras Merah + Labu', 'bahan' => 'Beras merah, labu kuning, minyak kelapa', 'kalori' => '90 kkal'],
            ];
            if ($needsExtraProtein) {
                $recipes[] = ['nama' => 'Puree Hati Ayam + Wortel', 'bahan' => 'Hati ayam, wortel, minyak zaitun', 'kalori' => '120 kkal'];
            }
            return $recipes;
        }

        if ($umur >= 9 && $umur <= 11) {
            $recipes = [
                ['nama' => 'Nasi Tim Ikan Salmon', 'bahan' => 'Beras, ikan salmon, bayam, minyak wijen', 'kalori' => '150 kkal'],
                ['nama' => 'Bubur Kacang Hijau + Telur', 'bahan' => 'Kacang hijau, telur, santan', 'kalori' => '140 kkal'],
            ];
            if ($needsExtraProtein) {
                $recipes[] = ['nama' => 'Sup Daging Sapi + Kentang', 'bahan' => 'Daging sapi cincang, kentang, wortel, brokoli', 'kalori' => '200 kkal'];
            }
            return $recipes;
        }

        // 12+ bulan
        $recipes = [
            ['nama' => 'Nasi + Ayam Suwir + Sayur Bening', 'bahan' => 'Nasi, ayam, bayam, jagung manis', 'kalori' => '250 kkal'],
            ['nama' => 'Perkedel Tahu + Telur Dadar', 'bahan' => 'Tahu, telur, kentang, wortel parut', 'kalori' => '200 kkal'],
        ];
        if ($needsExtraProtein) {
            $recipes[] = ['nama' => 'Nasi + Rendang Daging + Telur Rebus', 'bahan' => 'Nasi, daging sapi, santan, bumbu rendang, telur', 'kalori' => '350 kkal'];
        }
        return $recipes;
    }
}
