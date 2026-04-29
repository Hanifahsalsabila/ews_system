<?php

namespace App\Services;

class WhoZscoreService
{
    /**
     * Calculate Z-Scores based on WHO 2006 Standards.
     * This is a simplified version using a small lookup table for demonstration.
     * In a production environment, this should use the full WHO dataset (L, M, S values).
     */
    public function calculate($type, $value, $ageInMonths, $gender)
    {
        // Placeholder lookup table (Median and SD for age 0-60 months)
        // Format: [age] => [Median, SD_lower, SD_upper]
        // This is just for demonstration of the 'Enterprise' feature logic.
        $standard = $this->getStandard($type, $ageInMonths, $gender);
        
        if (!$standard) return null;

        $median = $standard['m'];
        $sd = ($value < $median) ? $standard['sd_lower'] : $standard['sd_upper'];

        if ($sd == 0) return 0;
        
        return round(($value - $median) / $sd, 2);
    }

    private function getStandard($type, $age, $gender)
    {
        // Simulated WHO Table Data
        // Real logic would query a database table with pre-loaded WHO data
        $data = [
            'BBU' => [
                'L' => [0 => ['m' => 3.3, 'sd_lower' => 0.4, 'sd_upper' => 0.5], 12 => ['m' => 9.6, 'sd_lower' => 1.0, 'sd_upper' => 1.2]],
                'P' => [0 => ['m' => 3.2, 'sd_lower' => 0.4, 'sd_upper' => 0.5], 12 => ['m' => 8.9, 'sd_lower' => 1.0, 'sd_upper' => 1.1]],
            ],
            'TBU' => [
                'L' => [0 => ['m' => 49.9, 'sd_lower' => 1.9, 'sd_upper' => 2.0], 12 => ['m' => 75.7, 'sd_lower' => 2.6, 'sd_upper' => 2.8]],
                'P' => [0 => ['m' => 49.1, 'sd_lower' => 1.9, 'sd_upper' => 2.0], 12 => ['m' => 74.0, 'sd_lower' => 2.6, 'sd_upper' => 2.8]],
            ],
        ];

        // Find the closest age if not exact
        $ageRange = array_keys($data[$type][$gender]);
        $closestAge = 0;
        foreach ($ageRange as $a) {
            if ($age >= $a) $closestAge = $a;
        }

        return $data[$type][$gender][$closestAge] ?? null;
    }

    public function getStatusBBU($zscore)
    {
        if ($zscore < -3) return 'Sangat Kurang (Severely Underweight)';
        if ($zscore < -2) return 'Kurang (Underweight)';
        if ($zscore > 2) return 'Risiko Berat Badan Lebih';
        return 'Normal';
    }

    public function getStatusTBU($zscore)
    {
        if ($zscore < -3) return 'Sangat Pendek (Severely Stunted)';
        if ($zscore < -2) return 'Pendek (Stunted)';
        if ($zscore > 3) return 'Tinggi';
        return 'Normal';
    }
}
