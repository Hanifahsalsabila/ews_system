<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$service = app(App\Services\WhoZscoreService::class);

echo "--- 4. AGE BOUNDARY ---\n";
$edges = [1826.00, 1826.24, 1826.25, 1826.26, 1826.49, 1826.50, 1827.00];
foreach ($edges as $edge) {
    $dobDate = \Carbon\Carbon::parse('2020-01-01 00:00:00');
    $measDate = (clone $dobDate)->addSeconds((int)round($edge * 86400));
    $res = $service->calculateBBTB(18.3, 110.0, $dobDate->format('Y-m-d H:i:s'), $measDate->format('Y-m-d H:i:s'), 'L');
    echo sprintf("%.2f days => Z: %s, Status: %s\n", $edge, $res['zscore'] ?? 'null', $res['status']);
}

echo "\n--- 5. 731-DAY ROUTING ---\n";
$edges = [730.49, 730.50, 730.99, 731.00, 731.49, 731.50];
foreach ($edges as $edge) {
    $dobDate = \Carbon\Carbon::parse('2020-01-01 00:00:00');
    $measDate = (clone $dobDate)->addSeconds((int)round($edge * 86400));
    $res = $service->calculateBBTB(10.0, 80.0, $dobDate->format('Y-m-d H:i:s'), $measDate->format('Y-m-d H:i:s'), 'L');
    echo sprintf("%.2f days => Route: %s, Rounded Age: %d\n", $edge, $res['indicator_used'] ?? 'N/A', $res['age_in_days']);
}

echo "\n--- 6. MEASUREMENT POSITION ---\n";
$dobDate = \Carbon\Carbon::parse('2020-01-01 00:00:00');
$measDate1 = (clone $dobDate)->addDays(730); // <731
$res1 = $service->calculateBBTB(10.0, 80.0, $dobDate->format('Y-m-d H:i:s'), $measDate1->format('Y-m-d H:i:s'), 'L', 'berdiri');
echo "AGE <731, 80.0cm berdiri => " . ($res1['indicator_used'] ?? 'N/A') . " | Z: " . ($res1['zscore'] ?? 'N/A') . "\n";
// Let's also check normal recumbent for 730 days at 80.7 cm to see if it matches
$res1_norm = $service->calculateBBTB(10.0, 80.7, $dobDate->format('Y-m-d H:i:s'), $measDate1->format('Y-m-d H:i:s'), 'L', 'terlentang');
echo "AGE <731, 80.7cm terlentang => " . ($res1_norm['indicator_used'] ?? 'N/A') . " | Z: " . ($res1_norm['zscore'] ?? 'N/A') . " (Should match above)\n";

$measDate2 = (clone $dobDate)->addDays(731); // >=731
$res2 = $service->calculateBBTB(10.0, 80.0, $dobDate->format('Y-m-d H:i:s'), $measDate2->format('Y-m-d H:i:s'), 'L', 'terlentang');
echo "AGE >=731, 80.0cm terlentang => " . ($res2['indicator_used'] ?? 'N/A') . " | Z: " . ($res2['zscore'] ?? 'N/A') . "\n";
$res2_norm = $service->calculateBBTB(10.0, 79.3, $dobDate->format('Y-m-d H:i:s'), $measDate2->format('Y-m-d H:i:s'), 'L', 'berdiri');
echo "AGE >=731, 79.3cm berdiri => " . ($res2_norm['indicator_used'] ?? 'N/A') . " | Z: " . ($res2_norm['zscore'] ?? 'N/A') . " (Should match above)\n";

echo "\n--- 9. REFERENCE BOUNDARIES ---\n";
$res45 = $service->calculateBBTB(10.0, 45.0, clone $dobDate, (clone $dobDate)->addDays(100), 'L', 'terlentang');
$res110 = $service->calculateBBTB(10.0, 110.0, clone $dobDate, (clone $dobDate)->addDays(100), 'L', 'terlentang');
$res44 = $service->calculateBBTB(10.0, 44.9, clone $dobDate, (clone $dobDate)->addDays(100), 'L', 'terlentang');
$res111 = $service->calculateBBTB(10.0, 110.1, clone $dobDate, (clone $dobDate)->addDays(100), 'L', 'terlentang');
echo "WFL 45.0 => " . (isset($res45['zscore']) ? "valid" : "invalid") . "\n";
echo "WFL 110.0 => " . (isset($res110['zscore']) ? "valid" : "invalid") . "\n";
echo "WFL <45.0 => " . (isset($res44['zscore']) ? "valid" : "invalid") . "\n";
echo "WFL >110.0 => " . (isset($res111['zscore']) ? "valid" : "invalid") . "\n";

$res65 = $service->calculateBBTB(10.0, 65.0, clone $dobDate, (clone $dobDate)->addDays(1000), 'L', 'berdiri');
$res120 = $service->calculateBBTB(10.0, 120.0, clone $dobDate, (clone $dobDate)->addDays(1000), 'L', 'berdiri');
$res64 = $service->calculateBBTB(10.0, 64.9, clone $dobDate, (clone $dobDate)->addDays(1000), 'L', 'berdiri');
$res121 = $service->calculateBBTB(10.0, 120.1, clone $dobDate, (clone $dobDate)->addDays(1000), 'L', 'berdiri');
echo "WFH 65.0 => " . (isset($res65['zscore']) ? "valid" : "invalid") . "\n";
echo "WFH 120.0 => " . (isset($res120['zscore']) ? "valid" : "invalid") . "\n";
echo "WFH <65.0 => " . (isset($res64['zscore']) ? "valid" : "invalid") . "\n";
echo "WFH >120.0 => " . (isset($res121['zscore']) ? "valid" : "invalid") . "\n";
