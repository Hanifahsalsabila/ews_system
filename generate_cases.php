<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$service = app(App\Services\WhoZscoreService::class);

$cases = [
    // BBU (WAZ) - Exact boundary
    ['indicator'=>'BBU', 'gender'=>'L', 'dob'=>'2024-01-01', 'meas'=>'2024-01-01', 'weight'=>3.3, 'height'=>0, 'pos'=>''],
    ['indicator'=>'BBU', 'gender'=>'P', 'dob'=>'2024-01-01', 'meas'=>'2024-01-31', 'weight'=>4.2, 'height'=>0, 'pos'=>''], // ~1 month
    // BBU (WAZ) - In between months
    ['indicator'=>'BBU', 'gender'=>'L', 'dob'=>'2024-01-01', 'meas'=>'2024-01-16', 'weight'=>4.0, 'height'=>0, 'pos'=>''], // ~15 days
    ['indicator'=>'BBU', 'gender'=>'P', 'dob'=>'2024-01-01', 'meas'=>'2024-02-15', 'weight'=>5.0, 'height'=>0, 'pos'=>''], // ~45 days
    
    // TBU (HAZ) - Exact boundary
    ['indicator'=>'TBU', 'gender'=>'L', 'dob'=>'2024-01-01', 'meas'=>'2024-01-01', 'weight'=>0, 'height'=>50.0, 'pos'=>'terlentang'],
    // TBU (HAZ) - In between months
    ['indicator'=>'TBU', 'gender'=>'L', 'dob'=>'2024-01-01', 'meas'=>'2024-03-15', 'weight'=>0, 'height'=>60.0, 'pos'=>'terlentang'], // ~75 days
    // TBU - 0.7cm correction tests
    ['indicator'=>'TBU', 'gender'=>'P', 'dob'=>'2024-01-01', 'meas'=>'2025-07-01', 'weight'=>0, 'height'=>75.0, 'pos'=>'berdiri'], // 18m, standing
    ['indicator'=>'TBU', 'gender'=>'P', 'dob'=>'2024-01-01', 'meas'=>'2025-07-01', 'weight'=>0, 'height'=>75.0, 'pos'=>'terlentang'], // 18m, recumbent
    ['indicator'=>'TBU', 'gender'=>'L', 'dob'=>'2024-01-01', 'meas'=>'2027-01-01', 'weight'=>0, 'height'=>96.0, 'pos'=>'terlentang'], // 36m, recumbent
    ['indicator'=>'TBU', 'gender'=>'L', 'dob'=>'2024-01-01', 'meas'=>'2027-01-01', 'weight'=>0, 'height'=>96.0, 'pos'=>'berdiri'], // 36m, standing

    // BBTB (WFL) - exact 0.5 cm
    ['indicator'=>'BBTB', 'gender'=>'L', 'dob'=>'2024-01-01', 'meas'=>'2024-07-01', 'weight'=>8.0, 'height'=>68.0, 'pos'=>'terlentang'],
    // BBTB (WFL) - intermediate
    ['indicator'=>'BBTB', 'gender'=>'P', 'dob'=>'2024-01-01', 'meas'=>'2024-07-01', 'weight'=>8.0, 'height'=>68.2, 'pos'=>'terlentang'],
    
    // BBTB (WFH) - exact 0.5 cm
    ['indicator'=>'BBTB', 'gender'=>'L', 'dob'=>'2024-01-01', 'meas'=>'2027-01-01', 'weight'=>14.0, 'height'=>96.0, 'pos'=>'berdiri'],
    // BBTB (WFH) - intermediate
    ['indicator'=>'BBTB', 'gender'=>'P', 'dob'=>'2024-01-01', 'meas'=>'2027-01-01', 'weight'=>14.0, 'height'=>96.2, 'pos'=>'berdiri'],
    
    // Extreme Z-score test > 3
    ['indicator'=>'BBTB', 'gender'=>'L', 'dob'=>'2024-01-01', 'meas'=>'2024-07-01', 'weight'=>12.0, 'height'=>68.0, 'pos'=>'terlentang'],
    // Extreme Z-score test < -3
    ['indicator'=>'BBTB', 'gender'=>'P', 'dob'=>'2024-01-01', 'meas'=>'2024-07-01', 'weight'=>4.0, 'height'=>68.0, 'pos'=>'terlentang'],

    // Out of range length
    ['indicator'=>'BBTB', 'gender'=>'L', 'dob'=>'2024-01-01', 'meas'=>'2024-07-01', 'weight'=>3.0, 'height'=>40.0, 'pos'=>'terlentang'], // WFL < 45
    ['indicator'=>'BBTB', 'gender'=>'L', 'dob'=>'2024-01-01', 'meas'=>'2027-01-01', 'weight'=>20.0, 'height'=>125.0, 'pos'=>'berdiri'], // WFH > 120
    
    // 24 month boundary tests
    ['indicator'=>'BBTB', 'gender'=>'L', 'dob'=>'2024-01-01', 'meas'=>'2025-12-30', 'weight'=>12.0, 'height'=>85.0, 'pos'=>'terlentang'], // 729 days
    ['indicator'=>'BBTB', 'gender'=>'L', 'dob'=>'2024-01-01', 'meas'=>'2025-12-31', 'weight'=>12.0, 'height'=>85.0, 'pos'=>'terlentang'], // 730 days
    ['indicator'=>'BBTB', 'gender'=>'L', 'dob'=>'2024-01-01', 'meas'=>'2026-01-01', 'weight'=>12.0, 'height'=>85.0, 'pos'=>'berdiri'], // 731 days
    ['indicator'=>'BBTB', 'gender'=>'L', 'dob'=>'2024-01-01', 'meas'=>'2026-01-02', 'weight'=>12.0, 'height'=>85.0, 'pos'=>'berdiri'], // 732 days

    // BBTB mapping legacy test (without age if possible, but calculateBBTB requires dob)
    // We will just demonstrate standard BBTB
    ['indicator'=>'BBTB', 'gender'=>'L', 'dob'=>'2024-01-01', 'meas'=>'2028-01-01', 'weight'=>15.0, 'height'=>100.0, 'pos'=>'berdiri'],
    
    // Generate some random valid cases to reach 30
    ['indicator'=>'BBU', 'gender'=>'L', 'dob'=>'2024-01-01', 'meas'=>'2024-10-05', 'weight'=>9.0, 'height'=>0, 'pos'=>''],
    ['indicator'=>'BBU', 'gender'=>'P', 'dob'=>'2024-01-01', 'meas'=>'2024-05-18', 'weight'=>6.5, 'height'=>0, 'pos'=>''],
    ['indicator'=>'TBU', 'gender'=>'L', 'dob'=>'2024-01-01', 'meas'=>'2025-02-22', 'weight'=>0, 'height'=>78.3, 'pos'=>'terlentang'],
    ['indicator'=>'TBU', 'gender'=>'P', 'dob'=>'2024-01-01', 'meas'=>'2026-08-14', 'weight'=>0, 'height'=>91.4, 'pos'=>'berdiri'],
    ['indicator'=>'BBTB', 'gender'=>'L', 'dob'=>'2024-01-01', 'meas'=>'2024-11-11', 'weight'=>10.5, 'height'=>75.5, 'pos'=>'terlentang'],
    ['indicator'=>'BBTB', 'gender'=>'P', 'dob'=>'2024-01-01', 'meas'=>'2027-03-03', 'weight'=>13.8, 'height'=>94.7, 'pos'=>'berdiri'],
    ['indicator'=>'BBU', 'gender'=>'L', 'dob'=>'2024-01-01', 'meas'=>'2028-12-31', 'weight'=>18.0, 'height'=>0, 'pos'=>''], // ~59.9 months
];

echo "| # | Sex | Age Days | Weight | Measured Length/Height | Position | Indicator | E-Posyandu Z |\n";
echo "|---|---|---:|---:|---:|---|---|---:|\n";

foreach ($cases as $i => $c) {
    $z = null;
    $age = $service->calculateAgeInDays($c['dob'], $c['meas']);
    
    if ($c['indicator'] == 'BBU') {
        $res = $service->calculateBBU($c['weight'], $c['dob'], $c['meas'], $c['gender']);
        $z = $res['raw_zscore'] ?? $res['status'];
    } elseif ($c['indicator'] == 'TBU') {
        $res = $service->calculateTBU($c['height'], $c['dob'], $c['meas'], $c['gender'], $c['pos']);
        $z = $res['raw_zscore'] ?? $res['status'];
    } elseif ($c['indicator'] == 'BBTB') {
        $res = $service->calculateBBTB($c['weight'], $c['height'], $c['dob'], $c['meas'], $c['gender'], $c['pos']);
        $z = $res['raw_zscore'] ?? $res['status'];
    }
    
    if (is_numeric($z)) {
        $z = round($z, 2);
    }
    
    $pos = $c['pos'] ?: '-';
    $val = $c['indicator'] == 'BBU' ? $c['weight'] : $c['height'];
    if ($c['indicator'] == 'BBTB') $val = $c['height']; // show length
    
    echo sprintf("| %d | %s | %d | %.1f | %.1f | %s | %s | %s |\n",
        $i+1, $c['gender'], $age, $c['weight'], $val, $pos, $c['indicator'], $z
    );
}
