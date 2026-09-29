<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\WhoZscoreService;
use Carbon\Carbon;

$service = new WhoZscoreService();

function testCase($service, $ageInDays, $heightCm, $weightKg, $posisiUkur) {
    $meas = Carbon::now()->startOfDay();
    $hours = $ageInDays * 24.0;
    $mins = (int) round($hours * 60);
    $dob = $meas->copy()->subMinutes($mins);

    $dobStr = $dob->format('Y-m-d H:i:s');
    $measStr = $meas->format('Y-m-d H:i:s');

    echo "--- Edge Case ---\n";
    $exact = $service->calculateContinuousAgeInDays($dobStr, $measStr);
    $months = $service->calculateAgeInMonthsFraction($dobStr, $measStr);
    $rounded = $service->roundAgeForLookup($exact);
    
    echo "Exact age days: " . $exact . "\n";
    echo "Age in months: " . $months . "\n";
    echo "Rounded age days: " . $rounded . "\n";
    
    $res = $service->calculateBBTB($weightKg, $heightCm, $dobStr, $measStr, 'L', $posisiUkur);
    
    echo "Route: " . ($res['indicator_used'] ?? 'N/A') . "\n";
    echo "Z-score: " . ($res['zscore'] ?? 'NULL') . "\n";
    echo "Status: " . ($res['status'] ?? 'N/A') . "\n";
    echo "Flag: " . ($res['flag'] ? 'TRUE' : 'FALSE') . "\n";
}

testCase($service, 1826.24, 100, 15.0, 'berdiri');
testCase($service, 1826.25, 100, 15.0, 'berdiri');
testCase($service, 1827.00, 100, 15.0, 'berdiri');
