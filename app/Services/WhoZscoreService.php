<?php

namespace App\Services;

use App\Models\Pemeriksaan;
use App\Models\Warga;
use App\Models\WhoZscoreLms;
use Carbon\Carbon;

class WhoZscoreService
{
    public const ANTHRO_DAYS_OF_MONTH = 30.4375;
    /**
     * Calculate exact age in days between Date of Birth and Measurement Date.
     */
    public function calculateAgeInDays($dob, $measurementDate): int
    {
        $dobDate = Carbon::parse($dob);
        $measDate = Carbon::parse($measurementDate);

        if ($dobDate->greaterThan($measDate)) {
            return 0;
        }

        return (int) $dobDate->diffInDays($measDate);
    }

    /**
     * Calculate continuous exact age in days (retaining fractions if time exists or passed as months).
     */
    public function calculateContinuousAgeInDays($dob, $measurementDate): float
    {
        $dobDate = Carbon::parse($dob);
        $measDate = Carbon::parse($measurementDate);

        if ($dobDate->greaterThan($measDate)) {
            return 0.0;
        }

        // Calendar-day difference if it's exact calendar dates
        if ($dobDate->format('H:i:s') === '00:00:00' && $measDate->format('H:i:s') === '00:00:00') {
            return (float) $dobDate->diffInDays($measDate);
        }

        // Fraction if time exists
        return $dobDate->diffInRealHours($measDate) / 24.0;
    }

    /**
     * WHO standard rounding: fraction < 0.5 -> floor, fraction >= 0.5 -> ceil.
     */
    public function roundAgeForLookup(float $exactDays): int
    {
        $fraction = $exactDays - floor($exactDays);
        if ($fraction < 0.5) {
            return (int) floor($exactDays);
        }
        return (int) ceil($exactDays);
    }

    /**
     * Calculate fractional age in months (WHO standard: 30.4375 days per month).
     */
    public function calculateAgeInMonthsFraction($dob, $measurementDate): float
    {
        $days = $this->calculateContinuousAgeInDays($dob, $measurementDate);

        return $days / self::ANTHRO_DAYS_OF_MONTH;
    }

    /**
     * Retrieve LMS parameters for indicator, gender, and reference value.
     * Uses linear interpolation if exact point is not found.
     */
    public function getLmsParameters(string $indicator, string $gender, float $referenceValue): ?array
    {
        // Normalize gender
        $normalizedGender = in_array(strtoupper($gender), ['L', 'M', 'BOY', 'MALE']) ? 'L' : 'P';

        // Map indicator aliases
        $normalizedIndicator = match (strtoupper($indicator)) {
            'BBU', 'WAZ', 'WEIGHT_FOR_AGE' => 'WAZ',
            'TBU', 'HAZ', 'LAZ', 'HEIGHT_FOR_AGE', 'LENGTH_FOR_AGE' => 'HAZ',
            'WFL', 'WEIGHT_FOR_LENGTH' => 'WFL',
            'WFH', 'WEIGHT_FOR_HEIGHT' => 'WFH',
            'BBTB', 'WHZ', 'WLZ' => 'WFL', // Default to WFL if unspecific, callers specify WFL vs WFH
            default => strtoupper($indicator)
        };

        // Try exact match first
        $exact = WhoZscoreLms::whereIn('indicator', [$normalizedIndicator, $indicator])
            ->where('gender', $normalizedGender)
            ->where('ref_value', $referenceValue)
            ->where('source_version', 'who_anthro_v1.1.0')
            ->where('unit', in_array($normalizedIndicator, ['WAZ', 'HAZ']) ? 'age_days' : 'cm')
            ->first();

        if ($exact && $exact->m > 0) {
            return [
                'l' => (float) $exact->l,
                'm' => (float) $exact->m,
                's' => (float) $exact->s,
                'is_interpolated' => false,
            ];
        }

        // Perform linear interpolation
        return $this->getInterpolatedLms($normalizedIndicator, $normalizedGender, $referenceValue);
    }

    /**
     * Linear interpolation of L, M, S parameters between adjacent reference points.
     * Rejects extrapolation if referenceValue is outside WHO table range.
     */
    public function getInterpolatedLms(string $indicator, string $gender, float $referenceValue): ?array
    {
        // Out of reference range (no extrapolation allowed)
        if ($referenceValue < 45.0 || $referenceValue > 120.0) {
            return null;
        }

        $unit = in_array($indicator, ['WAZ', 'HAZ']) ? 'age_days' : 'cm';

        // Precise linear interpolation for WFL/WFH (0.1 cm spacing)
        $lowerRef = floor($referenceValue * 10) / 10;
        $upperRef = floor($referenceValue * 10 + 1) / 10;
        
        $lower = WhoZscoreLms::where('indicator', $indicator)
            ->where('gender', $gender)
            ->where('ref_value', '<=', $referenceValue)
            ->where('source_version', 'who_anthro_v1.1.0')
            ->where('unit', $unit)
            ->orderBy('ref_value', 'desc')
            ->first();

        $upper = WhoZscoreLms::where('indicator', $indicator)
            ->where('gender', $gender)
            ->where('ref_value', '>=', $referenceValue)
            ->where('source_version', 'who_anthro_v1.1.0')
            ->where('unit', $unit)
            ->orderBy('ref_value', 'asc')
            ->first();

        if (! $lower || ! $upper) {
            return null;
        }

        $x1 = (float) $lower->ref_value;
        $x2 = (float) $upper->ref_value;

        if (abs($x2 - $x1) < 0.0001) {
            return [
                'l' => (float) $lower->l,
                'm' => (float) $lower->m,
                's' => (float) $lower->s,
                'is_interpolated' => false,
            ];
        }

        // Interpolasi spesifik 0.1 spacing sesuai constraint
        if ($indicator === 'WFL' || $indicator === 'WFH') {
            $x1 = $lowerRef;
            $x2 = $upperRef;
            $alpha = ($referenceValue - $x1) / 0.1;
        } else {
            $alpha = ($referenceValue - $x1) / ($x2 - $x1);
        }

        $l = (float) $lower->l + $alpha * ((float) $upper->l - (float) $lower->l);
        $m = (float) $lower->m + $alpha * ((float) $upper->m - (float) $lower->m);
        $s = (float) $lower->s + $alpha * ((float) $upper->s - (float) $lower->s);

        return [
            'l' => $l,
            'm' => $m,
            's' => $s,
            'is_interpolated' => true,
        ];
    }

    /**
     * Calculate Z-Score using standard WHO LMS Box-Cox Transformation.
     */
    public function calculateZScore(float $measurement, float $L, float $M, float $S): ?float
    {
        if ($measurement <= 0 || $M <= 0 || $S <= 0) {
            return null;
        }

        if (abs($L) < 0.0001) {
            $zscore = log($measurement / $M) / $S;
        } else {
            $zscore = (pow(($measurement / $M), $L) - 1.0) / ($L * $S);
        }

        return $zscore;
    }

    /**
     * Apply Extreme-Z adjustment for WAZ, WFL, WFH (Not HAZ)
     */
    public function applyExtremeZAdjustment(float $measurement, float $L, float $M, float $S, float $rawZ): float
    {
        if ($rawZ > 3) {
            $sd2pos = $M * pow((1 + $L * $S * 2), (1 / $L));
            $sd3pos = $M * pow((1 + $L * $S * 3), (1 / $L));
            $sd23pos = $sd3pos - $sd2pos;
            return 3 + ($measurement - $sd3pos) / $sd23pos;
        }
        
        if ($rawZ < -3) {
            $sd2neg = $M * pow((1 + $L * $S * -2), (1 / $L));
            $sd3neg = $M * pow((1 + $L * $S * -3), (1 / $L));
            $sd23neg = $sd2neg - $sd3neg;
            return -3 + ($measurement - $sd3neg) / $sd23neg;
        }

        return $rawZ;
    }

    /**
     * Calculate BB/U (WAZ - Weight-for-Age).
     */
    public function calculateBBU($weightKg, $dob, $measurementDate, $gender): array
    {
        if ($weightKg <= 0 || ! $dob || ! $measurementDate) {
            return ['zscore' => null, 'status' => 'Data tidak valid', 'age_in_days' => 0];
        }

        $exactDays = $this->calculateContinuousAgeInDays($dob, $measurementDate);
        $ageInDays = (int) floor($exactDays); // Backward compatibility fallback output

        // Continuous validity check: 60 completed months max valid
        if ($this->calculateAgeInMonthsFraction($dob, $measurementDate) >= 60) {
            return ['zscore' => null, 'status' => 'out_of_reference_range', 'age_in_days' => $ageInDays, 'flag' => false];
        }

        $roundedAge = $this->roundAgeForLookup($exactDays);
        $lms = $this->getLmsParameters('WAZ', $gender, $roundedAge);

        if (! $lms) {
            return ['zscore' => null, 'status' => 'out_of_reference_range', 'age_in_days' => $ageInDays, 'flag' => false];
        }

        $rawZ = $this->calculateZScore($weightKg, $lms['l'], $lms['m'], $lms['s']);

        if ($rawZ === null) {
            return ['zscore' => null, 'status' => 'Error kalkulasi', 'age_in_days' => $ageInDays, 'flag' => false];
        }

        $adjZ = $this->applyExtremeZAdjustment($weightKg, $lms['l'], $lms['m'], $lms['s'], $rawZ);
        $displayZ = round($adjZ, 2);
        $flag = ($adjZ < -6 || $adjZ > 5);

        return [
            'zscore' => $displayZ,
            'raw_zscore' => round($rawZ, 4),
            'status' => $this->getStatusBBU($displayZ),
            'age_in_days' => $ageInDays,
            'flag' => $flag,
        ];
    }

    /**
     * Calculate TB/U or PB/U (HAZ/LAZ - Height/Length-for-Age).
     * Applies WHO 0.7 cm recumbent/standing adjustment when required.
     */
    public function calculateTBU($heightCm, $dob, $measurementDate, $gender, $posisiUkur = 'terlentang'): array
    {
        if ($heightCm <= 0 || ! $dob || ! $measurementDate) {
            return ['zscore' => null, 'status' => 'Data tidak valid', 'age_in_days' => 0];
        }

        $exactDays = $this->calculateContinuousAgeInDays($dob, $measurementDate);
        $ageInDays = (int) floor($exactDays); 

        // Continuous validity check: 60 completed months max valid
        if ($this->calculateAgeInMonthsFraction($dob, $measurementDate) >= 60) {
            return ['zscore' => null, 'status' => 'out_of_reference_range', 'age_in_days' => $ageInDays, 'flag' => false];
        }

        $roundedAge = $this->roundAgeForLookup($exactDays);
        $adjustedHeight = (float) $heightCm;
        $measurementType = 'LENGTH';

        if ($roundedAge < 731) {
            $measurementType = 'LENGTH';
            if (in_array(strtolower($posisiUkur), ['berdiri', 'height', 'standing'])) {
                $adjustedHeight += 0.7; // Convert standing height to recumbent length
            }
        } else {
            $measurementType = 'HEIGHT';
            if (in_array(strtolower($posisiUkur), ['terlentang', 'length', 'recumbent'])) {
                $adjustedHeight -= 0.7; // Convert recumbent length to standing height
            }
        }

        $lms = $this->getLmsParameters('HAZ', $gender, $roundedAge);

        if (! $lms) {
            return ['zscore' => null, 'status' => 'out_of_reference_range', 'age_in_days' => $ageInDays, 'flag' => false];
        }

        $rawZ = $this->calculateZScore($adjustedHeight, $lms['l'], $lms['m'], $lms['s']);

        if ($rawZ === null) {
            return ['zscore' => null, 'status' => 'Error kalkulasi', 'age_in_days' => $ageInDays, 'flag' => false];
        }

        // HAZ DOES NOT use extreme adjustment
        $displayZ = round($rawZ, 2);
        $flag = (abs($rawZ) > 6);

        return [
            'zscore' => $displayZ,
            'raw_zscore' => round($rawZ, 4),
            'status' => $this->getStatusTBU($displayZ),
            'adjusted_height' => $adjustedHeight,
            'age_in_days' => $ageInDays,
            'measurement_type' => $measurementType,
            'flag' => $flag,
        ];
    }

    /**
     * Calculate BB/TB or BB/PB (WFL/WFH / WHZ/WLZ - Weight-for-Length/Height).
     * Reference parameter is Length/Height in cm (not age).
     */
    public function calculateBBTB($weightKg, $heightCm, $dob, $measurementDate, $gender, $posisiUkur = 'terlentang'): array
    {
        if ($weightKg <= 0 || $heightCm <= 0 || ! $dob || ! $measurementDate) {
            return ['zscore' => null, 'status' => 'Data tidak valid', 'age_in_days' => 0];
        }

        $exactDays = $this->calculateContinuousAgeInDays($dob, $measurementDate);
        $ageInDays = (int) floor($exactDays);
        $roundedAge = $this->roundAgeForLookup($exactDays);

        if ($this->calculateAgeInMonthsFraction($dob, $measurementDate) >= 60) {
            return [
                'zscore' => null,
                'status' => 'out_of_reference_range',
                'age_in_days' => $ageInDays,
                'flag' => false
            ];
        }

        $adjustedHeight = (float) $heightCm;
        $indicatorUsed = 'WFL';
        $measurementType = 'LENGTH';

        if ($roundedAge < 731) {
            $indicatorUsed = 'WFL'; // Weight-for-Length (45 - 110 cm)
            $measurementType = 'LENGTH';
            if (in_array(strtolower($posisiUkur), ['berdiri', 'height', 'standing'])) {
                $adjustedHeight += 0.7;
            }
        } else {
            $indicatorUsed = 'WFH'; // Weight-for-Height (65 - 120 cm)
            $measurementType = 'HEIGHT';
            if (in_array(strtolower($posisiUkur), ['terlentang', 'length', 'recumbent'])) {
                $adjustedHeight -= 0.7;
            }
        }

        // Validity constraint
        if ($indicatorUsed === 'WFL' && ($adjustedHeight < 45.0 || $adjustedHeight > 110.0)) {
            return ['zscore' => null, 'status' => 'out_of_reference_range', 'age_in_days' => $ageInDays, 'flag' => false];
        }
        if ($indicatorUsed === 'WFH' && ($adjustedHeight < 65.0 || $adjustedHeight > 120.0)) {
            return ['zscore' => null, 'status' => 'out_of_reference_range', 'age_in_days' => $ageInDays, 'flag' => false];
        }

        $lms = $this->getLmsParameters($indicatorUsed, $gender, $adjustedHeight);

        if (! $lms) {
            return ['zscore' => null, 'status' => 'out_of_reference_range', 'age_in_days' => $ageInDays, 'flag' => false];
        }

        $rawZ = $this->calculateZScore($weightKg, $lms['l'], $lms['m'], $lms['s']);

        if ($rawZ === null) {
            return ['zscore' => null, 'status' => 'Error kalkulasi', 'age_in_days' => $ageInDays, 'flag' => false];
        }

        $adjZ = $this->applyExtremeZAdjustment($weightKg, $lms['l'], $lms['m'], $lms['s'], $rawZ);
        $displayZ = round($adjZ, 2);
        $flag = (abs($adjZ) > 5);

        return [
            'zscore' => $displayZ,
            'raw_zscore' => round($rawZ, 4),
            'status' => $this->getStatusBBTB($displayZ),
            'indicator_used' => $indicatorUsed,
            'measurement_type' => $measurementType,
            'age_in_days' => $ageInDays,
            'flag' => $flag,
        ];
    }

    /**
     * Standard WHO Status Classification for BB/U (Weight-for-Age).
     */
    public function getStatusBBU($zscore): string
    {
        if ($zscore === null) return '-';
        if ($zscore < -3) return 'Sangat Kurang (Severely Underweight)';
        if ($zscore < -2) return 'Kurang (Underweight)';
        if ($zscore > 2) return 'Risiko Berat Badan Lebih';
        return 'Normal';
    }

    /**
     * Standard WHO Status Classification for TB/U (Height-for-Age).
     */
    public function getStatusTBU($zscore): string
    {
        if ($zscore === null) return '-';
        if ($zscore < -3) return 'Sangat Pendek (Severely Stunted)';
        if ($zscore < -2) return 'Pendek (Stunted)';
        if ($zscore > 3) return 'Tinggi';
        return 'Normal';
    }

    /**
     * Standard WHO Status Classification for BB/TB (Weight-for-Height/Length).
     */
    public function getStatusBBTB($zscore): string
    {
        if ($zscore === null) return '-';
        if ($zscore < -3) return 'Gizi Buruk (Severely Wasted)';
        if ($zscore < -2) return 'Gizi Kurang (Wasted)';
        if ($zscore > 3) return 'Obesi (Obese)';
        if ($zscore > 2) return 'Gizi Lebih (Overweight)';
        if ($zscore > 1) return 'Berisiko Gizi Lebih';
        return 'Gizi Baik (Normal)';
    }

    /**
     * Backwards-compatible generic calculate method.
     */
    public function calculate($type, $value, $refValue, $gender)
    {
        $lms = $this->getLmsParameters($type, $gender, (float) $refValue);

        if (! $lms) {
            return null;
        }

        $rawZ = $this->calculateZScore((float) $value, $lms['l'], $lms['m'], $lms['s']);

        return $rawZ !== null ? round($rawZ, 2) : null;
    }

    /**
     * Mesin Keputusan EWS untuk menentukan 4 Tingkat Risiko.
     */
    public function evaluateEWS(Warga $warga, $zBbu, $zTbu, $currentWeight)
    {
        $pastExams = Pemeriksaan::where('warga_id', $warga->id)
            ->whereNotNull('berat_badan')
            ->orderByDesc('created_at')
            ->take(3)
            ->pluck('berat_badan')
            ->toArray();

        $history = array_merge([$currentWeight], $pastExams);
        $history = array_slice($history, 0, 4);

        $tCount = 0;
        for ($i = 0; $i < count($history) - 1; $i++) {
            if ($history[$i] <= $history[$i+1]) {
                $tCount++;
            } else {
                break;
            }
        }

        if (($zBbu !== null && $zBbu < -3) || ($zTbu !== null && $zTbu < -3) || $tCount >= 3) {
            return [
                'status' => 'bahaya',
                'message' => "Z-Score sangat rendah (<-3 SD) atau BB tidak naik 3x (3T). Rekomendasi: Rujukan Segera ke Puskesmas."
            ];
        }

        if (($zBbu !== null && $zBbu < -2) || ($zTbu !== null && $zTbu < -2) || $tCount == 2) {
            return [
                'status' => 'risiko_tinggi',
                'message' => "Indikasi Stunting/Wasting ringan (Z-Score <-2) atau BB tidak naik 2x (2T). Rekomendasi: Otomatis Kunjungan Rumah Kader."
            ];
        }

        if ($tCount == 1) {
            return [
                'status' => 'waspada',
                'message' => "Berat Badan tidak naik 1x (1T). Rekomendasi: Edukasi Gizi dan pantau bulan depan."
            ];
        }

        return [
            'status' => 'normal',
            'message' => "Pertumbuhan baik."
        ];
    }
}
