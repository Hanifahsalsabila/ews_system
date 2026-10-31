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
     * @param Warga $warga
     * @param float|null $zBbu (Weight-for-Age / BB/U)
     * @param float|null $zTbu (Height/Length-for-Age / TB/U)
     * @param float|null $zBbTb (Weight-for-Height/Length / BB/TB)
     * @param float|null $currentWeight Berat badan saat ini (kg)
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
        ?float $currentWeight,
        ?int $currentPemeriksaanId = null,
        ?string $dob = null,
        ?string $measurementDate = null
    ): array {
        // 1. Tentukan tanggal dan waktu pemeriksaan saat ini untuk membatasi riwayat
        $currentDate = $measurementDate;
        $currentCreatedAt = null;

        if ($currentPemeriksaanId) {
            $currentExam = Pemeriksaan::with('sesiPosyandu')->find($currentPemeriksaanId);
            if ($currentExam) {
                $sessionDate = $currentExam->sesiPosyandu?->tanggal;
                if ($sessionDate) {
                    $currentDate = Carbon::parse($sessionDate)->toDateString();
                } elseif ($currentExam->created_at) {
                    $currentDate = $currentExam->created_at->toDateString();
                }
                $currentCreatedAt = $currentExam->created_at;
            }
        }

        // 2. Ambil riwayat berat badan sebelumnya (HANYA PEMERIKSAAN MASA LALU YANG VALID)
        // Kriteria Keabsahan Riwayat:
        // - Berasal dari balita yang sama (warga_id dan tipe_kohort 'balita')
        // - Harus sudah tervalidasi (is_validated = true) oleh tenaga kesehatan
        // - Berat badan tidak null
        // - Tidak berstatus soft-deleted
        // - Mengecualikan pemeriksaan saat ini (mencegah bug self-comparison)
        // - Membatasi tanggal hanya pemeriksaan sebelum tanggal saat ini (mencegah anomali backdated)
        $pastQuery = Pemeriksaan::query()
            ->leftJoin('sesi_posyandu', 'pemeriksaan.sesi_posyandu_id', '=', 'sesi_posyandu.id')
            ->where('pemeriksaan.warga_id', $warga->id)
            ->where(function ($q) {
                $q->where('pemeriksaan.tipe_kohort', 'balita')
                  ->orWhereNull('pemeriksaan.tipe_kohort');
            })
            ->where('pemeriksaan.is_validated', true)
            ->whereNotNull('pemeriksaan.berat_badan')
            ->whereNull('pemeriksaan.deleted_at');

        if ($currentPemeriksaanId) {
            $pastQuery->where('pemeriksaan.id', '!=', $currentPemeriksaanId);
        }

        if ($currentDate) {
            $pastQuery->where(function ($q) use ($currentDate, $currentCreatedAt, $currentPemeriksaanId) {
                $q->whereRaw('DATE(COALESCE(sesi_posyandu.tanggal, pemeriksaan.created_at)) < ?', [$currentDate]);
                if ($currentPemeriksaanId) {
                    $q->orWhere(function ($sameDateQ) use ($currentDate, $currentCreatedAt, $currentPemeriksaanId) {
                        $sameDateQ->whereRaw('DATE(COALESCE(sesi_posyandu.tanggal, pemeriksaan.created_at)) = ?', [$currentDate]);
                        if ($currentCreatedAt) {
                            $sameDateQ->where(function ($tieQ) use ($currentCreatedAt, $currentPemeriksaanId) {
                                $tieQ->where('pemeriksaan.created_at', '<', $currentCreatedAt)
                                     ->orWhere(function ($idQ) use ($currentCreatedAt, $currentPemeriksaanId) {
                                         $idQ->where('pemeriksaan.created_at', '=', $currentCreatedAt)
                                             ->where('pemeriksaan.id', '<', $currentPemeriksaanId);
                                     });
                            });
                        } else {
                            $sameDateQ->where('pemeriksaan.id', '<', $currentPemeriksaanId);
                        }
                    });
                }
            });
        }

        // Urutkan berdasarkan tanggal sesi posyandu sebenarnya, dengan fallback created_at dan id
        $pastWeights = $pastQuery
            ->orderByRaw('DATE(COALESCE(sesi_posyandu.tanggal, pemeriksaan.created_at)) DESC')
            ->orderByDesc('pemeriksaan.created_at')
            ->orderByDesc('pemeriksaan.id')
            ->take(3)
            ->pluck('pemeriksaan.berat_badan')
            ->map(fn($w) => (float) $w)
            ->toArray();

        // 3. Hitung tren kenaikan berat badan (1T, 2T, 3T)
        // Definisi operasional klinis KMS Kemenkes:
        // 1T: Berat badan tidak naik 1x dibandingkan penimbangan sebelumnya yang valid
        // 2T: Berat badan tidak naik 2x berturut-turut (Indikasi growth faltering / risiko tinggi)
        // 3T: Berat badan tidak naik 3x berturut-turut (Kegagalan tumbuh kronis / bahaya rujukan)
        // Jika belum ada riwayat sebelumnya, $tCount = 0 (tidak ada self-comparison).
        $tCount = 0;
        $history = [];
        $hasCurrentWeight = ($currentWeight !== null && (float) $currentWeight > 0);

        if ($hasCurrentWeight) {
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
        } else {
            $history = $pastWeights;
        }

        // 4. Evaluasi Ketersediaan dan Kelengkapan Data Antropometri
        $hasCompleteZscores = ($zBbu !== null && $zTbu !== null && $zBbTb !== null);
        $messageReasons = [];

        // Evaluasi Indikator Kegawatdaruratan Akut & Kronis:
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
            if (! $hasCompleteZscores || ! $hasCurrentWeight) {
                $message .= " (Peringatan: Penilaian berdasarkan indikator kritis yang tersedia; beberapa data antropometri belum lengkap).";
            }
        } elseif ($isModerateAcuteWasting || $isModerateChronicStunting || $isModerateUnderweight || $tCount == 2) {
            $status = 'risiko_tinggi';
            if ($isModerateAcuteWasting) $messageReasons[] = "Gizi Kurang Akut (BB/TB < -2 SD)";
            if ($isModerateChronicStunting) $messageReasons[] = "Pendek/Stunting (TB/U < -2 SD)";
            if ($isModerateUnderweight) $messageReasons[] = "Berat Badan Kurang (BB/U < -2 SD)";
            if ($tCount == 2) $messageReasons[] = "Berat Badan tidak naik 2x berturut-turut (2T)";

            $message = "⚠️ RISIKO TINGGI: " . implode(' & ', $messageReasons) . ". Rekomendasi: Otomatis Kunjungan Rumah Kader & Intervensi PMT Pemulihan.";
            if (! $hasCompleteZscores || ! $hasCurrentWeight) {
                $message .= " (Peringatan: Penilaian berdasarkan indikator risiko yang tersedia; beberapa data antropometri belum lengkap).";
            }
        } elseif ($tCount == 1 || ($zBbTb !== null && $zBbTb > 2.0)) {
            $status = 'waspada';
            if ($tCount == 1) $messageReasons[] = "Berat Badan tidak naik 1x (1T)";
            if ($zBbTb !== null && $zBbTb > 2.0) $messageReasons[] = "Berisiko Gizi Lebih/Obesitas (BB/TB > 2 SD)";

            $message = "⚠️ WASPADA: " . implode(' & ', $messageReasons) . ". Rekomendasi: Konseling gizi dan pantau kembali sesi posyandu berikutnya.";
            if (! $hasCompleteZscores || ! $hasCurrentWeight) {
                $message .= " (Peringatan: Penilaian berdasarkan indikator parsial; beberapa data antropometri belum lengkap).";
            }
        } elseif (! $hasCompleteZscores || ! $hasCurrentWeight) {
            // DATA TIDAK LENGKAP: Tidak boleh diklasifikasikan sebagai 'normal'
            $status = 'data_tidak_lengkap';
            $missing = [];
            if ($zBbu === null) $missing[] = "BB/U (WAZ)";
            if ($zTbu === null) $missing[] = "TB/U (HAZ)";
            if ($zBbTb === null) $missing[] = "BB/TB (WHZ)";
            if (! $hasCurrentWeight) $missing[] = "Berat Badan";

            $missingStr = implode(', ', $missing);
            $message = "⚠️ DATA TIDAK LENGKAP: Indikator [{$missingStr}] belum tersedia atau kalkulasi gagal. Perlu pengukuran ulang sebelum status pertumbuhan dapat disimpulkan.";
        } else {
            $status = 'normal';
            $message = "Pertumbuhan balita terpantau normal dan sesuai standar WHO.";
        }

        // 5. Hitung Usia dalam Bulan untuk Model Prioritas
        $ageInMonths = 12; // Fallback
        if ($dob && $measurementDate) {
            $dobCarbon = Carbon::parse($dob);
            $measCarbon = Carbon::parse($measurementDate);
            $ageInMonths = (int) $dobCarbon->diffInMonths($measCarbon);
        } elseif ($warga->tanggal_lahir) {
            $ageInMonths = (int) Carbon::parse($warga->tanggal_lahir)->diffInMonths(now());
        }

        // 6. Pemeringkatan Prioritas Risiko (Layer 2 - Multi-Criteria / SAW Model)
        $priorityData = $this->calculatePriorityScore($status, $zBbu, $zTbu, $zBbTb, $tCount, $ageInMonths);

        return [
            'status' => $status,
            'message' => $message,
            't_count' => $tCount,
            'history' => $history,
            'priority_score' => $priorityData['score'],
            'priority_details' => $priorityData['details'],
            'is_alert' => in_array($status, ['bahaya', 'risiko_tinggi', 'waspada']),
            'alert_status' => in_array($status, ['bahaya', 'risiko_tinggi', 'waspada']) ? 'pending' : 'resolved',
        ];
    }

    /**
     * Hitung Skor Prioritas Risiko (0.00 - 100.00) menggunakan Model Multi-Kriteria Transparan (SAW).
     *
     * Kriteria dan Bobot Konfigurasi Awal Sistem:
     * C1: Defisit Akut Wasting (WHZ) - Bobot 0.35 (35%) -> Risiko mortalitas jangka pendek tertinggi.
     * C2: Defisit Bobot Underweight (WAZ) - Bobot 0.25 (25%) -> Indikator komposit malnutrisi global.
     * C3: Defisit Kronis Stunting (HAZ) - Bobot 0.20 (20%) -> Indikator defisit linier pertumbuhan tulang.
     * C4: Kegagalan Tren Pertumbuhan (tCount: 1T/2T/3T) - Bobot 0.15 (15%) -> Kecepatan penurunan berat badan KMS.
     * C5: Kerentanan Usia Balita (<= 24 bulan) - Bobot 0.05 (5%) -> Periode emas 1000 Hari Pertama Kehidupan.
     *
     * Total Bobot: 0.35 + 0.25 + 0.20 + 0.15 + 0.05 = 1.00 (100%).
     * Catatan Metodologis: Bobot ini merupakan konfigurasi baseline sistem dan masih memerlukan validasi pakar gizi/klinisi.
     */
    public function calculatePriorityScore(
        string $status,
        ?float $zBbu,
        ?float $zTbu,
        ?float $zBbTb,
        int $tCount,
        int $ageInMonths
    ): array {
        // Base category separation: Menjaga integritas triase klinis
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
                    'composite_deficit' => 0.0,
                    'added_score' => 0.0,
                    'summary' => 'Pertumbuhan normal, tidak memerlukan prioritas intervensi darurat.',
                ]
            ];
        }

        if ($status === 'data_tidak_lengkap') {
            return [
                'score' => 0.00,
                'details' => [
                    'category' => 'data_tidak_lengkap',
                    'base_score' => 0.0,
                    'composite_deficit' => 0.0,
                    'added_score' => 0.0,
                    'summary' => 'Data antropometri belum lengkap, evaluasi ditunda hingga pemeriksaan ulang.',
                ]
            ];
        }

        // Normalisasi defisit Z-score (0.0 s/d 1.0)
        // Mengubah atribut cost (makin negatif makin parah) menjadi skala benefit prioritas (0.0 s/d 1.0)
        $normWhz = 0.0;
        if ($zBbTb !== null && $zBbTb < -2.0) {
            $normWhz = max(0.0, min(1.0, (-2.0 - $zBbTb) / 2.0));
        }

        $normWaz = 0.0;
        if ($zBbu !== null && $zBbu < -2.0) {
            $normWaz = max(0.0, min(1.0, (-2.0 - $zBbu) / 2.0));
        }

        $normHaz = 0.0;
        if ($zTbu !== null && $zTbu < -2.0) {
            $normHaz = max(0.0, min(1.0, (-2.0 - $zTbu) / 2.0));
        }

        // Normalisasi T-count (0.0 s/d 1.0)
        $normT = match (true) {
            $tCount >= 3 => 1.0,
            $tCount == 2 => 0.67,
            $tCount == 1 => 0.33,
            default => 0.0,
        };

        // Normalisasi Usia (Metodologi Periode 1000 HPK):
        // Balita <= 24 bulan berada pada periode plastisitas otak dan risiko stunting kritis (nilai normalisasi = 1.0).
        // Balita > 24 bulan (25-59 bulan) tetap memerlukan pemantauan tetapi defisit linier lebih stabil (nilai normalisasi = 0.5).
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

        if (is_nan($finalScore) || is_infinite($finalScore)) {
            $finalScore = 0.00;
        }
        $finalScore = max(0.00, min(100.00, $finalScore));

        $factors = [];
        if ($normWhz > 0) $factors[] = "Defisit Akut BB/TB (" . round($zBbTb, 2) . ")";
        if ($normHaz > 0) $factors[] = "Defisit Stunting TB/U (" . round($zTbu, 2) . ")";
        if ($normWaz > 0) $factors[] = "Defisit Bobot BB/U (" . round($zBbu, 2) . ")";
        if ($tCount > 0) $factors[] = "Tren {$tCount}T";
        if ($ageInMonths <= 24) $factors[] = "Periode Emas (<=24 bln)";

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
     * Status 'resolved' hanya boleh ditetapkan melalui proses tindak lanjut yang sah.
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

    /**
     * Tentukan status siklus alert EWS dengan menjaga integritas riwayat dan audit trail.
     *
     * Aturan Transisi Mesin Status Alert:
     * 1. Jika alert sudah diselesaikan secara sah oleh Bidan (resolved_by !== null atau resolved_at !== null):
     *    - Status 'resolved' dan jejak audit trail (resolved_at, resolved_by) TIDAK BOLEH dihapus/diubah oleh evaluasi ulang.
     * 2. Jika alert sedang dalam penanganan Bidan ('in_progress'):
     *    - Status 'in_progress' HARUS DIPERTAHANKAN apapun hasil evaluasi barunya.
     * 3. Jika alert berstatus 'pending':
     *    - Evaluasi yang berubah menjadi 'normal' atau 'data_tidak_lengkap' TIDAK BOLEH menyelesaikan alert secara otomatis.
     *    - Alert tetap 'pending' sampai ditindaklanjuti secara sah oleh Bidan.
     * 4. Jika record baru (belum ada data sebelumnya):
     *    - Jika berisiko ('bahaya', 'risiko_tinggi', 'waspada') => 'pending'.
     *    - Jika tidak berisiko ('normal', 'data_tidak_lengkap') => 'resolved' (tanpa resolved_by/at).
     * 5. Jika record lama non-risiko ('resolved' tanpa tindakan bidan / resolved_by is null) dan berubah menjadi berisiko:
     *    - Transisi menjadi 'pending' untuk memicu perhatian klinis Bidan.
     *
     * @param KalkulasiZScore|null $existing
     * @param string $newEwsStatus
     * @return array
     */
    public function determineAlertLifecycle(?KalkulasiZScore $existing, string $newEwsStatus): array
    {
        $isRisk = in_array($newEwsStatus, ['bahaya', 'risiko_tinggi', 'waspada']);

        // 1. Pemeriksaan baru (belum ada kalkulasi zscore sebelumnya)
        if (! $existing) {
            return [
                'alert_status' => $isRisk ? 'pending' : 'resolved',
                'resolved_at' => null,
                'resolved_by' => null,
            ];
        }

        // 2. Alert sudah diselesaikan oleh Bidan secara sah (Audit trail tercatat)
        if ($existing->alert_status === 'resolved' && ($existing->resolved_by !== null || $existing->resolved_at !== null)) {
            return [
                'alert_status' => 'resolved',
                'resolved_at' => $existing->resolved_at,
                'resolved_by' => $existing->resolved_by,
            ];
        }

        // 3. Alert sedang dalam proses tindak lanjut oleh Bidan (in_progress)
        if ($existing->alert_status === 'in_progress') {
            return [
                'alert_status' => 'in_progress',
                'resolved_at' => $existing->resolved_at,
                'resolved_by' => $existing->resolved_by,
            ];
        }

        // 4. Alert sedang menunggu tindakan Bidan (pending)
        if ($existing->alert_status === 'pending') {
            // Jangan pernah otomatis me-resolve alert pending saat evaluasi menjadi normal atau data_tidak_lengkap
            return [
                'alert_status' => 'pending',
                'resolved_at' => null,
                'resolved_by' => null,
            ];
        }

        // 5. Record lama awalnya non-risiko tanpa intervensi bidan (resolved_by null), lalu berubah berisiko
        if ($isRisk) {
            return [
                'alert_status' => 'pending',
                'resolved_at' => null,
                'resolved_by' => null,
            ];
        }

        // Tetap non-alert resolved
        return [
            'alert_status' => 'resolved',
            'resolved_at' => null,
            'resolved_by' => null,
        ];
    }

    /**
     * Sinkronisasi data KalkulasiZScore dan Alert Lifecycle untuk suatu Pemeriksaan.
     *
     * @param Pemeriksaan $pemeriksaan
     * @param array $payload Data kalkulasi Z-Score (zscore_bb_u, raw, flag, status, dll.)
     * @param array $ewsResult Hasil dari evaluate()
     * @return KalkulasiZScore
     */
    public function syncKalkulasiZScore(Pemeriksaan $pemeriksaan, array $payload, array $ewsResult): KalkulasiZScore
    {
        $existing = KalkulasiZScore::where('pemeriksaan_id', $pemeriksaan->id)->first();
        $lifecycle = $this->determineAlertLifecycle($existing, $ewsResult['status']);

        $mergedData = array_merge($payload, [
            'pemeriksaan_id' => $pemeriksaan->id,
            'ews_status' => $ewsResult['status'],
            'ews_message' => $ewsResult['message'],
            'priority_score' => $ewsResult['priority_score'],
            'priority_details' => $ewsResult['priority_details'],
            'alert_status' => $lifecycle['alert_status'],
            'resolved_at' => $lifecycle['resolved_at'],
            'resolved_by' => $lifecycle['resolved_by'],
        ]);

        return KalkulasiZScore::updateOrCreate(
            ['pemeriksaan_id' => $pemeriksaan->id],
            $mergedData
        );
    }
}
