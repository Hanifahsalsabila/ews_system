<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$service = app(App\Services\WhoZscoreService::class);

echo "\n### 1. WFH 90.25 cm / 13.0 kg ###\n";
// WFH 90.25, L
$reflection = new ReflectionClass($service);
$method = $reflection->getMethod('getLmsParameters');
$method->setAccessible(true);

$lms = $method->invoke($service, 'WFH', 'L', 90.25);
echo "Canonical interpolated L: {$lms['l']}\n";
echo "Canonical interpolated M: {$lms['m']}\n";
echo "Canonical interpolated S: {$lms['s']}\n";

$rawZ = $service->calculateZScore(13.0, $lms['l'], $lms['m'], $lms['s']);
echo "Independent expected raw Z: 0.052615\n";
echo "Actual E-Posyandu raw Z: " . $rawZ . "\n";
echo "Final rounded Z: " . round($rawZ, 2) . "\n";
echo "Delta: " . abs(0.052615 - $rawZ) . "\n";


echo "\n### 2. FLAG BOUNDARY ###\n";
// Function to print flag evidence
function testFlag($ind, $z, $expectedFlag) {
    // Generate weight/height to hit exactly or close to Z
    // Actually we can just call applyExtremeZAdjustment and check logic, or we can just calculateZScore in reverse?
    // We can just dump test cases manually if we know the LMS.
    global $service, $method;
    
    // For exact testing we can mock or just compute backward
    // Let's use WAZ (0 days, L) L=0.3487, M=3.3464, S=0.14602
    // It's easier to just pick weights that yield specific Z scores.
}

// Better yet, let's reverse calculate weights for desired Z scores.
function reverseZ($l, $m, $s, $z) {
    if ($l == 0) {
        return $m * exp($z * $s);
    }
    return $m * pow(1 + $l * $s * $z, 1 / $l);
}

function verifyFlag($ind, $gender, $ageOrHeight, $targetZ, $expectedFlag, $label, $isHaz = false) {
    global $service, $method;
    
    if ($ind == 'BBU' || $ind == 'TBU') {
        $lms = $method->invoke($service, $ind, $gender, $ageOrHeight);
    } else {
        $lms = $method->invoke($service, $ind, $gender, $ageOrHeight);
    }

    $val = reverseZ($lms['l'], $lms['m'], $lms['s'], $targetZ);

    if ($ind == 'BBU') {
        $res = $service->calculateBBU($val, '2020-01-01', date('Y-m-d', strtotime("2020-01-01 + " . round($ageOrHeight * 30.4375) . " days")), $gender);
    } elseif ($ind == 'TBU') {
        $res = $service->calculateTBU($val, '2020-01-01', date('Y-m-d', strtotime("2020-01-01 + " . round($ageOrHeight * 30.4375) . " days")), $gender, 'terlentang');
    } elseif ($ind == 'WFL') {
        // WFL needs age < 731
        $res = $service->calculateBBTB($val, $ageOrHeight, '2020-01-01', date('Y-m-d', strtotime("2020-01-01 + 100 days")), $gender, 'terlentang');
    } else {
        // WFH needs age >= 731
        $res = $service->calculateBBTB($val, $ageOrHeight, '2020-01-01', date('Y-m-d', strtotime("2020-01-01 + 1000 days")), $gender, 'berdiri');
    }

    $adjZ = isset($res['raw_zscore']) ? $res['raw_zscore'] : null;
    $flag = $res['flag'];
    $pass = ($flag === $expectedFlag) ? 'PASS' : 'FAIL';

    echo "[$ind] $label (Target Z: $targetZ)\n";
    echo "  Input: " . round($val, 4) . "\n";
    echo "  Raw Z: " . $targetZ . "\n"; 
    // wait, extreme z adjustment changes raw_zscore to adj Z in the calculation if extreme? No, calculateBBTB returns `raw_zscore` as raw, and `zscore` as adjusted/display.
    // Let's get the final zscore for adjusted
    echo "  Adjusted Z: " . ($res['zscore'] ?? 'N/A') . "\n";
    echo "  Flag: " . ($flag ? 'TRUE' : 'FALSE') . "\n";
    echo "  Expected Flag: " . ($expectedFlag ? 'TRUE' : 'FALSE') . "\n";
    echo "  Status: $pass\n\n";
}

// WAZ
echo "WAZ:\n";
verifyFlag('BBU', 'L', 0, -6.0, false, "-6 boundary");
verifyFlag('BBU', 'L', 0, -6.01, true, "below -6");
verifyFlag('BBU', 'L', 0, 5.0, false, "5 boundary");
verifyFlag('BBU', 'L', 0, 5.01, true, "above 5");

// HAZ
echo "HAZ:\n";
verifyFlag('TBU', 'L', 0, -6.0, false, "-6 boundary", true);
verifyFlag('TBU', 'L', 0, -6.01, true, "below -6", true);
verifyFlag('TBU', 'L', 0, 6.0, false, "6 boundary", true);
verifyFlag('TBU', 'L', 0, 6.01, true, "above 6", true);

// WFL
echo "WFL:\n";
verifyFlag('WFL', 'L', 68.0, -5.0, false, "-5 boundary");
verifyFlag('WFL', 'L', 68.0, -5.01, true, "below -5");
verifyFlag('WFL', 'L', 68.0, 5.0, false, "5 boundary");
verifyFlag('WFL', 'L', 68.0, 5.01, true, "above 5");

// WFH
echo "WFH:\n";
verifyFlag('WFH', 'L', 90.0, -5.0, false, "-5 boundary");
verifyFlag('WFH', 'L', 90.0, -5.01, true, "below -5");
verifyFlag('WFH', 'L', 90.0, 5.0, false, "5 boundary");
verifyFlag('WFH', 'L', 90.0, 5.01, true, "above 5");

