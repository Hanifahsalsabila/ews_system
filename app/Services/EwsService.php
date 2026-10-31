<?php

namespace App\Services;

use App\Models\KalkulasiZScore;
use App\Models\KunjunganRumah;
use App\Models\Pemeriksaan;
use App\Models\Warga;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class EwsService
{
    /**
     * Mesin Keputusan EWS Terpadu untuk menentukan Kategori Risiko dan Prioritas.
     *
     * @param Warga $warga
     * @param float|null $zBbu (Weight-for-Age / BB/U)
     * @param float|null $zTbu (Height/Length-for-Age / TB/U)
     * @param float|null $zBbTb (Weight-for-Height/Length / BB/TB)
     * @param float $currentWeight Berat badan saat ini (kg)
     * @param int|null $currentPemeriksaanId ID pemeriksaan saat ini (untuk dikecualikan dari riwayat)
     * @param string|null $dob Tanggal lahir
     * @param string|null $measurementDate Tanggal pengukuran/sesi
     * @return array
     */
    public function evaluate(
        Warga $warga,
        ?float $zBbu,
        ?float $zTbu,
        ?float $zBbTb,
        float $currentWeight,
        ?int $currentPemeriksaanId = null,
        ?string $dob = null,
        ?string $measurementDate = null
    ): array {
        // 1. Ambil riwayat berat badan sebelumnya (PAST EXAMS SAJA)
        // Mengecualikan pemeriksaan saat ini untuk mencegah bug self-comparison
        $pastQuery = Pemeriksaan::where('warga_id', $warga->id)
            ->whereNotNull('berat_badan');

        if ($currentPemeriksaanId) {
            $pastQuery->where('id', '!=', $currentPemeriksaanId);
        }

        $pastWeights = $pastQuery->orderByDesc('created_at')
            ->orderByDesc('id')
            ->take(3)
            ->pluck('berat_badan')
            ->map(fn($w) => (float) $w)
            ->toArray();

        // 2. Hitung tren kenaikan berat badan (1T, 2T, 3T)
        // Jika belum ada riwayat (pemeriksaan pertama), $tCount = 0
        $tCount = 0;
        $history = array_merge([(float) $currentWeight], $pastWeights);
        $history = array_slice($history, 0, 4);

        if (count($pastWeights) > 0) {
            for ($i = 0; $i < count($history) - 1; $i++) {
                if ($history[$i] <= $history[$i + 1]) {
                    $tCount++;
                } else {
                    break; // Terjadi kenaikan bobot, rantai T terputus
                }
            }
        }

        // 3. Klasifikasi Kategori Risiko (Layer 1 - Pedoman Klinis)
        $status = 'normal';
        $messageReasons = [];

        // Evaluasi Indikator Akut & Kronis:
        $isSevereAcuteWasting = ($zBbTb !== null && $zBbTb < -3.0);
        $isSevereChronicStunting = ($zTbu !== null && $zTbu < -3.0);
        $isSevereUnderweight = ($zBbu !== null && $zBbu < -3.0);

        $isModerateAcuteWasting = ($zBbTb !== null && $zBbTb < -2.0);
        $isModerateChronicStunting = ($zTbu !== null && $zTbu < -2.0);
        $isModerateUnderweight = ($zBbu !== null && $zBbu < -2.0);

        if ($isSevereAcuteWasting || $isSevereChronicStunting || $isSevereUnderweight || $tCount >= 3) {
            $status = 'bahaya';
            if ($isSevereAcuteWasting) $messageReasons[] = "Gizi Buruk Akut (BB/TB < -3 SD)";
            if ($isSevereChronicStunting) $messageReasons[] = "Sangat Pendek/Stunting Berat (TB/U < -3 SD)";
            if ($isSevereUnderweight) $messageReasons[] = "Sangat Kurang (BB/U < -3 SD)";
            if ($tCount >= 3) $messageReasons[] = "Berat Badan tidak naik 3x berturut-turut (3T)";
            
            $message = "⚠️ BAHAYA: " . implode(' & ', $messageReasons) . ". Rekomendasi: Rujukan Segera ke Puskesmas / Tindakan Medis Darurat.";
        } elseif ($isModerateAcuteWasting || $isModerateChronicStunting || $isModerateUnderweight || $tCount == 2) {
            $status = 'risiko_tinggi';
            if ($isModerateAcuteWasting) $messageReasons[] = "Gizi Kurang Akut (BB/TB < -2 SD)";
            if ($isModerateChronicStunting) $messageReasons[] = "Pendek/Stunting (TB/U < -2 SD)";
            if ($isModerateUnderweight) $messageReasons[] = "Berat Badan Kurang (BB/U < -2 SD)";
            if ($tCount == 2) $messageReasons[] = "Berat Badan tidak naik 2x berturut-turut (2T)";

            $message = "⚠️ RISIKO TINGGI: " . implode(' & ', $messageReasons) . ". Rekomendasi: Otomatis Kunjungan Rumah Kader & Intervensi PMT Pemulihan.";
        } elseif ($tCount == 1 || ($zBbTb !== null && $zBbTb > 2.0)) {
            $status = 'waspada';
            if ($tCount == 1) $messageReasons[] = "Berat Badan tidak naik 1x (1T)";
            if ($zBbTb !== null && $zBbTb > 2.0) $messageReasons[] = "Berisiko Gizi Lebih/Obesitas (BB/TB > 2 SD)";

            $message = "⚠️ WASPADA: " . implode(' & ', $messageReasons) . ". Rekomendasi: Konseling gizi dan pantau kembali sesi posyandu berikutnya.";
        } else {
            $status = 'normal';
            $message = "Pertumbuhan balita terpantau normal dan sesuai standar WHO.";
        }

        // 4. Hitung Usia dalam Bulan untuk Model Prioritas
        $ageInMonths = 12; // Fallback
        if ($dob && $measurementDate) {
            $dobCarbon = Carbon::parse($dob);
            $measCarbon = Carbon::parse($measurementDate);
            $ageInMonths = (int) $dobCarbon->diffInMonths($measCarbon);
        } elseif ($warga->tanggal_lahir) {
            $ageInMonths = (int) Carbon::parse($warga->tanggal_lahir)->diffInMonths(now());
        }

        // 5. Pemeringkatan Prioritas Risiko (Layer 2 - Multi-Criteria / SAW Model)
        $priorityData = $this->calculatePriorityScore($status, $zBbu, $zTbu, $zBbTb, $tCount, $ageInMonths);

        return [
            'status' => $status,
            'message' => $message,
            't_count' => $tCount,
            'history' => $history,
            'priority_score' => $priorityData['score'],
            'priority_details' => $priorityData['details'],
            'alert_status' => in_array($status, ['bahaya', 'risiko_tinggi', 'waspada']) ? 'pending' : 'resolved',
        ];
    }

    /**
     * Hitung Skor Prioritas Risiko (0.00 - 100.00) menggunakan Model Multi-Kriteria Transparan.
     *
     * Kriteria:
     * C1: Defisit Akut Wasting (WHZ) - Bobot 35% (kegawatdaruratan mortalitas tertinggi)
     * C2: Defisit Bobot Underweight (WAZ) - Bobot 25%
     * C3: Defisit Kronis Stunting (HAZ) - Bobot 20%
     * C4: Kegagalan Tren Pertumbuhan (tCount: 1T/2T/3T) - Bobot 15%
     * C5: Kerentanan Usia Periode Emas (<= 24 bulan) - Bobot 5%
     */
    public function calculatePriorityScore(
        string $status,
        ?float $zBbu,
        ?float $zTbu,
        ?float $zBbTb,
        int $tCount,
        int $ageInMonths
    ): array {
        // Base category separation: Menjaga integritas kategori klinis
        $baseScore = match ($status) {
            'bahaya' => 70.0,
            'risiko_tinggi' => 40.0,
            'waspada' => 15.0,
            default => 0.0,
        };

        if ($status === 'normal') {
            return [
                'score' => 0.00,
                'details' => [
                    'category' => 'normal',
                    'base_score' => 0.0,
                    'deficit_score' => 0.0,
                    'summary' => 'Pertumbuhan normal, tidak memerlukan prioritas intervensi darurat.',
                ]
            ];
        }

        // Normalisasi defisit Z-score (0.0 s/d 1.0)
        // Ambil defisit di bawah ambang normal (-2.0), maksimal 1.0 jika mencapai -4.0 SD atau lebih buruk
        $normWhz = 0.0;
        if ($zBbTb !== null && $zBbTb < -2.0) {
            $normWhz = min(1.0, (-2.0 - $zBbTb) / 2.0);
        }

        $normWaz = 0.0;
        if ($zBbu !== null && $zBbu < -2.0) {
            $normWaz = min(1.0, (-2.0 - $zBbu) / 2.0);
        }

        $normHaz = 0.0;
        if ($zTbu !== null && $zTbu < -2.0) {
            $normHaz = min(1.0, (-2.0 - $zTbu) / 2.0);
        }

        // Normalisasi T-count (0.0 s/d 1.0)
        $normT = match (true) {
            $tCount >= 3 => 1.0,
            $tCount == 2 => 0.67,
            $tCount == 1 => 0.33,
            default => 0.0,
        };

        // Normalisasi Usia (1.0 jika <= 24 bulan / 1000 HPK, 0.5 jika > 24 bulan)
        $normAge = ($ageInMonths <= 24) ? 1.0 : 0.5;

        // Bobot Kriteria
        $wWhz = 0.35;
        $wWaz = 0.25;
        $wHaz = 0.20;
        $wT   = 0.15;
        $wAge = 0.05;

        $compositeDeficit = ($wWhz * $normWhz) +
                            ($wWaz * $normWaz) +
                            ($wHaz * $normHaz) +
                            ($wT * $normT) +
                            ($wAge * $normAge);

        // Rentang skala penambahan dalam kategori adalah 29.9 poin
        $addedScore = round($compositeDeficit * 29.9, 2);
        $finalScore = round(min(100.0, $baseScore + $addedScore), 2);

        $factors = [];
        if ($normWhz > 0) $factors[] = "Defisit Akut BB/TB (" . round($zBbTb, 2) . ")";
        if ($normHaz > 0) $factors[] = "Defisit Stunting TB/U (" . round($zTbu, 2) . ")";
        if ($normWaz > 0) $factors[] = "Defisit Bobot BB/U (" . round($zBbu, 2) . ")";
        if ($tCount > 0) $factors[] = "Tren {$tCount}T";
        if ($ageInMonths <= 24) $factors[] = "Periode Emas (<24 bln)";

        return [
            'score' => $finalScore,
            'details' => [
                'category' => $status,
                'base_score' => $baseScore,
                'composite_deficit' => round($compositeDeficit, 4),
                'added_score' => $addedScore,
                'factors' => $factors,
                'summary' => count($factors) > 0 ? implode(', ', $factors) : 'Risiko terisolasi',
            ]
        ];
    }

    /**
     * Pemicu Otomatisasi Tiket Kunjungan Rumah EWS.
     * Mencegah duplikasi dan memastikan nilai ENUM basis data valid.
     */
    public function triggerKunjunganRumah(Warga $warga, array $ewsResult, ?int $petugasId = null): ?KunjunganRumah
    {
        if (! in_array($ewsResult['status'], ['risiko_tinggi', 'bahaya'])) {
            return null;
        }

        // Cegah pembuatan tiket duplikat jika balita sudah memiliki kunjungan aktif
        $existingVisit = KunjunganRumah::where('warga_id', $warga->id)
            ->whereIn('status', ['dijadwalkan', 'ditunda'])
            ->exists();

        if ($existingVisit) {
            return null;
        }

        // Pemetaan ENUM yang valid sesuai migrasi kunjungan_rumahs: ['rendah', 'sedang', 'tinggi', 'kritis']
        $prioritasEnum = ($ewsResult['status'] === 'bahaya') ? 'kritis' : 'tinggi';

        return KunjunganRumah::create([
            'warga_id' => $warga->id,
            'bidan_id' => $petugasId,
            'tipe' => '2t',
            'prioritas' => $prioritasEnum,
            'tanggal_rencana' => now()->addDays(3)->toDateString(),
            'status' => 'dijadwalkan',
            'catatan_sebelum' => "⚠️ PERINGATAN OTOMATIS (EWS): {$ewsResult['message']} Skor Prioritas: {$ewsResult['priority_score']}/100. Segera lakukan kunjungan rumah untuk intervensi gizi.",
        ]);
    }

    /**
     * Resolusi Alert EWS ketika Bidan mencatat tindak lanjut medis.
     */
    public function resolveAlert(Pemeriksaan $pemeriksaan, ?string $tindakan, ?string $catatan, int $userId): bool
    {
        $pemeriksaan->update([
            'tindakan' => $tindakan,
            'catatan' => $catatan,
        ]);

        if ($pemeriksaan->kalkulasiZscore) {
            $pemeriksaan->kalkulasiZscore->update([
                'alert_status' => 'resolved',
                'resolved_at' => now(),
                'resolved_by' => $userId,
            ]);
        }

        return true;
    }
}
