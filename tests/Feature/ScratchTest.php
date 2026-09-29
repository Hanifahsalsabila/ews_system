<?php
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Services\WhoZscoreService;
use Carbon\Carbon;
use Database\Seeders\WhoAnthroV110Seeder;

class ScratchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(WhoAnthroV110Seeder::class);
    }

    public function test_edge()
    {
        $service = new WhoZscoreService();

        $cases = [1826.24, 1826.25, 1827.00];

        foreach ($cases as $ageInDays) {
            $meas = Carbon::now()->startOfDay();
            $hours = $ageInDays * 24.0;
            $mins = (int) round($hours * 60);
            $dob = $meas->copy()->subMinutes($mins);

            $dobStr = $dob->format('Y-m-d H:i:s');
            $measStr = $meas->format('Y-m-d H:i:s');

            echo "\n--- Edge Case $ageInDays ---\n";
            $exact = $service->calculateContinuousAgeInDays($dobStr, $measStr);
            $months = $service->calculateAgeInMonthsFraction($dobStr, $measStr);
            $rounded = $service->roundAgeForLookup($exact);
            
            echo "Exact age days: " . $exact . "\n";
            echo "Age in months: " . $months . "\n";
            echo "Rounded age days: " . $rounded . "\n";
            
            $res = $service->calculateBBTB(15.0, 100, $dobStr, $measStr, 'L', 'berdiri');
            
            echo "Route: " . ($res['indicator_used'] ?? 'N/A') . "\n";
            echo "Z-score: " . ($res['zscore'] ?? 'NULL') . "\n";
            echo "Status: " . ($res['status'] ?? 'N/A') . "\n";
            echo "Flag: " . (isset($res['flag']) ? ($res['flag'] ? 'TRUE' : 'FALSE') : 'N/A') . "\n";
        }
        $this->assertTrue(true);
    }
}
