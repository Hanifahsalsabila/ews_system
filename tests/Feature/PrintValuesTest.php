<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\WhoZscoreService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use ReflectionClass;

class PrintValuesTest extends TestCase
{
    use RefreshDatabase;
    protected $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\WhoAnthroV110Seeder::class);
        $this->service = app(WhoZscoreService::class);
    }

    public function reverseZ($l, $m, $s, $z) {
        if ($l == 0) return $m * exp($z * $s);
        return $m * pow(1 + $l * $s * $z, 1 / $l);
    }

    public function verifyFlag($ind, $gender, $ageOrHeight, $targetZ, $expectedFlag, $label) {
        $reflection = new ReflectionClass($this->service);
        $method = $reflection->getMethod('getLmsParameters');
        $method->setAccessible(true);
        $lms = $method->invoke($this->service, $ind, $gender, (float)$ageOrHeight);
        
        $val = $this->reverseZ($lms['l'], $lms['m'], $lms['s'], $targetZ);

        if ($ind == 'BBU') {
            $res = $this->service->calculateBBU($val, '2020-01-01', date('Y-m-d', strtotime("2020-01-01 + " . round($ageOrHeight * 30.4375) . " days")), $gender);
        } elseif ($ind == 'TBU') {
            $res = $this->service->calculateTBU($val, '2020-01-01', date('Y-m-d', strtotime("2020-01-01 + " . round($ageOrHeight * 30.4375) . " days")), $gender, 'terlentang');
        } elseif ($ind == 'WFL') {
            $res = $this->service->calculateBBTB($val, $ageOrHeight, '2020-01-01', date('Y-m-d', strtotime("2020-01-01 + 100 days")), $gender, 'terlentang');
        } else {
            $res = $this->service->calculateBBTB($val, $ageOrHeight, '2020-01-01', date('Y-m-d', strtotime("2020-01-01 + 1000 days")), $gender, 'berdiri');
        }

        $adjZ = $res['zscore'] ?? 'N/A';
        $flag = $res['flag'];
        $pass = ($flag === $expectedFlag) ? 'PASS' : 'FAIL';

        echo "[$ind] $label (Target Z: $targetZ)\n";
        echo "  Input: " . round($val, 4) . "\n";
        echo "  Raw Z: " . sprintf('%.15f', $res['raw_zscore']) . "\n";
        echo "  Adjusted Z: $adjZ\n";
        echo "  Flag: " . ($flag ? 'TRUE' : 'FALSE') . "\n";
        echo "  Expected Flag: " . ($expectedFlag ? 'TRUE' : 'FALSE') . "\n";
        echo "  Status: $pass\n\n";

        $this->assertSame(
            $expectedFlag,
            $flag,
            "Flag tidak sesuai untuk {$ind}: {$label}. Raw Z = {$res['raw_zscore']}"
        );
    }

    public function test_print()
    {
        echo "\n### 1. WFH 90.25 cm / 13.0 kg ###\n";
        $reflection = new ReflectionClass($this->service);
        $method = $reflection->getMethod('getLmsParameters');
        $method->setAccessible(true);
        $lms = $method->invoke($this->service, 'WFH', 'L', 90.25);
        echo "Canonical interpolated L: {$lms['l']}\n";
        echo "Canonical interpolated M: {$lms['m']}\n";
        echo "Canonical interpolated S: {$lms['s']}\n";
        $rawZ = $this->service->calculateZScore(13.0, $lms['l'], $lms['m'], $lms['s']);
        echo "Independent expected raw Z: 0.052615\n";
        echo "Actual E-Posyandu raw Z: " . $rawZ . "\n";
        echo "Final rounded Z: " . round($rawZ, 2) . "\n";
        echo "Delta: " . abs(0.052615 - $rawZ) . "\n";

        echo "\n### 2. FLAG BOUNDARY ###\n";
        $this->verifyFlag('BBU', 'L', 0, -6.0, false, "-6 boundary");
        $this->verifyFlag('BBU', 'L', 0, -6.01, true, "below -6");
        $this->verifyFlag('BBU', 'L', 0, 5.0, false, "5 boundary");
        $this->verifyFlag('BBU', 'L', 0, 5.01, true, "above 5");

        $this->verifyFlag('TBU', 'L', 0, -6.0, false, "-6 boundary");
        $this->verifyFlag('TBU', 'L', 0, -6.01, true, "below -6");
        $this->verifyFlag('TBU', 'L', 0, 6.0, false, "6 boundary");
        $this->verifyFlag('TBU', 'L', 0, 6.01, true, "above 6");

        $this->verifyFlag('WFL', 'L', 68.0, -5.0, false, "-5 boundary");
        $this->verifyFlag('WFL', 'L', 68.0, -5.01, true, "below -5");
        $this->verifyFlag('WFL', 'L', 68.0, 5.0, false, "5 boundary");
        $this->verifyFlag('WFL', 'L', 68.0, 5.01, true, "above 5");

        $this->verifyFlag('WFH', 'L', 90.0, -5.0, false, "-5 boundary");
        $this->verifyFlag('WFH', 'L', 90.0, -5.01, true, "below -5");
        $this->verifyFlag('WFH', 'L', 90.0, 5.0, false, "5 boundary");
        $this->verifyFlag('WFH', 'L', 90.0, 5.01, true, "above 5");

        $this->assertTrue(true);
    }
}
