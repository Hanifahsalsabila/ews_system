<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\WhoZscoreService;

$service = new WhoZscoreService();

echo "=== STEP 9 UAT SCRIPT ===\n";

function testUat($name, $result, $expectedZ, $expectedFlag) {
    echo "[$name]\n";
    if ($result['status'] === 'out_of_reference_range') {
        echo "  Result: out_of_reference_range\n";
    } else {
        echo "  Z-Score: {$result['zscore']} (Expected: {$expectedZ})\n";
        echo "  Raw Z: {$result['raw_zscore']}\n";
        echo "  Flag: " . ($result['flag'] ? 'TRUE' : 'FALSE') . " (Expected: {$expectedFlag})\n";
        echo "  Status: {$result['status']}\n";
        
        $zDiff = abs($result['zscore'] - $expectedZ);
        $pass = $zDiff <= 0.05 && ($result['flag'] === filter_var($expectedFlag, FILTER_VALIDATE_BOOLEAN));
        echo "  Pass: " . ($pass ? "YES" : "NO") . "\n";
    }
    echo "\n";
}

// 1. Normal
$res = $service->calculateBBU(10.5, '2025-01-01', '2026-01-01', 'L'); 
testUat("Normal WAZ <24mo (Boy 1yr, 10.5kg)", $res, 0.72, 'FALSE');

$res = $service->calculateTBU(75.0, '2025-01-01', '2026-01-01', 'L', 'terlentang'); 
testUat("Normal HAZ <24mo (Boy 1yr, 75cm recumbent)", $res, -0.27, 'FALSE');

$res = $service->calculateBBTB(10.5, 75.0, '2025-01-01', '2026-01-01', 'L', 'terlentang'); 
testUat("Normal WFL <24mo (Boy 1yr, 75cm, 10.5kg)", $res, 0.78, 'FALSE');

// 2. Boundary 731 hari
// Exactly 730 days
$res = $service->calculateBBTB(10.5, 75.0, '2024-01-01', '2025-12-31', 'P', 'terlentang'); // WFL
echo "[Boundary 730 days]\n";
echo "  Indicator: " . $res['indicator_used'] . "\n";
echo "  Measurement: " . $res['measurement_type'] . "\n\n";

// 731 days (rounded)
$res = $service->calculateBBTB(10.5, 75.0, '2024-01-01', '2026-01-02', 'P', 'terlentang'); // WFH
echo "[Boundary 731 days]\n";
echo "  Indicator: " . $res['indicator_used'] . "\n";
echo "  Measurement: " . $res['measurement_type'] . "\n\n";

// 3. Measurement Position
echo "[Measurement Position Test (23 months - WFL)]\n";
$res1 = $service->calculateTBU(80.0, '2024-01-01', '2025-11-01', 'L', 'terlentang');
$res2 = $service->calculateTBU(80.0, '2024-01-01', '2025-11-01', 'L', 'berdiri');
echo "  Recumbent Z: {$res1['zscore']} (Adjusted Height: {$res1['adjusted_height']})\n";
echo "  Standing Z: {$res2['zscore']} (Adjusted Height: {$res2['adjusted_height']})\n\n";

// 4. Reference range continuous 60 months
echo "[Reference Range > 60 months]\n";
$res = $service->calculateBBU(18.0, '2021-01-01', '2026-02-01', 'L'); // 61 months
echo "  Status: {$res['status']}\n\n";
