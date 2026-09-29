<?php

namespace Tests\Unit;

use App\Services\WhoZscoreService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WhoZscoreServiceTest extends TestCase
{
    use RefreshDatabase;

    protected WhoZscoreService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\WhoAnthroV110Seeder::class);
        $this->service = new WhoZscoreService();
    }

    /** Test 1: Exact age calculation */
    public function test_1_exact_age_calculation(): void
    {
        $dob = '2024-01-01';
        $measDate = '2024-01-31';
        $days = $this->service->calculateContinuousAgeInDays($dob, $measDate);

        $this->assertEquals(30, $days);
    }

    /** Test 2: Gender-specific LMS */
    public function test_2_gender_specific_lms(): void
    {
        // 12 days instead of 12 months
        $lmsBoys = $this->service->getLmsParameters('WAZ', 'L', 12.0);
        $lmsGirls = $this->service->getLmsParameters('WAZ', 'P', 12.0);

        $this->assertNotNull($lmsBoys);
        $this->assertNotNull($lmsGirls);
        $this->assertNotEquals($lmsBoys['m'], $lmsGirls['m']);
    }

    /** Test 3: Exact LMS reference point */
    public function test_3_exact_lms_reference_point(): void
    {
        // 182 days (~6 months)
        $lms = $this->service->getLmsParameters('WAZ', 'L', 183.0);

        $this->assertNotNull($lms);
        // M at day 183 for Boys WAZ is approx 7.9
        $this->assertEquals(7.9, round($lms['m'], 1));
        $this->assertFalse($lms['is_interpolated'] ?? false);
    }

    /** Test 4: Linear interpolation */
    public function test_4_linear_interpolation(): void
    {
        // WFL interpolation at 68.25 cm
        $lms = $this->service->getInterpolatedLms('WFL', 'L', 68.25);

        $this->assertNotNull($lms);
    }

    /** Test 5: L = 0 condition */
    public function test_5_l_equals_zero_condition(): void
    {
        $measurement = 10.0;
        $L = 0.0;
        $M = 10.0;
        $S = 0.1;

        $zscore = $this->service->calculateZScore($measurement, $L, $M, $S);

        $this->assertEquals(0.0, round($zscore, 2));
    }

    /** Test 6: WAZ calculation */
    public function test_6_waz_calculation(): void
    {
        $dob = '2024-01-01';
        $measDate = '2024-07-01'; // 182 days ~ 5.98 months
        $res = $this->service->calculateBBU(7.93, $dob, $measDate, 'L');

        $this->assertNotNull($res['zscore']);
        $this->assertEquals('Normal', $res['status']);
    }

    /** Test 7: HAZ/LAZ calculation */
    public function test_7_haz_laz_calculation(): void
    {
        $dob = '2024-01-01';
        $measDate = '2025-01-01'; // 366 days ~ 12.02 months
        $res = $this->service->calculateTBU(75.7, $dob, $measDate, 'L', 'terlentang');

        $this->assertNotNull($res['zscore']);
        $this->assertEquals('Normal', $res['status']);
    }

    /** Test 8: WFL calculation */
    public function test_8_wfl_calculation(): void
    {
        $dob = '2024-01-01';
        $measDate = '2024-07-01'; // < 24 months
        $res = $this->service->calculateBBTB(8.0, 68.0, $dob, $measDate, 'L', 'terlentang');

        $this->assertNotNull($res['zscore']);
        $this->assertEquals('WFL', $res['indicator_used']);
    }

    /** Test 9: WFH calculation */
    public function test_9_wfh_calculation(): void
    {
        $dob = '2021-01-01';
        $measDate = '2024-01-01'; // 36 months (> 24 months)
        $res = $this->service->calculateBBTB(14.0, 96.0, $dob, $measDate, 'L', 'berdiri');

        $this->assertNotNull($res['zscore']);
        $this->assertEquals('WFH', $res['indicator_used']);
    }

    /** Test 10: Length/height correction */
    public function test_10_length_height_correction(): void
    {
        $dob = '2024-01-01';
        $measDate = '2024-07-01'; // Age < 24 months
        // Measured standing (berdiri), should add +0.7 cm
        $resStanding = $this->service->calculateTBU(67.0, $dob, $measDate, 'L', 'berdiri');

        $this->assertEquals(67.7, $resStanding['adjusted_height']);
    }

    /** Test 11: Out-of-range reference */
    public function test_11_out_of_range_reference(): void
    {
        $dob = '2010-01-01';
        $measDate = '2024-01-01'; // ~14 years old (> 60 months)
        $res = $this->service->calculateBBU(25.0, $dob, $measDate, 'L');

        $this->assertNull($res['zscore']);
        $this->assertEquals('out_of_reference_range', $res['status']);
    }

    /** Test 12: Invalid measurement */
    public function test_12_invalid_measurement(): void
    {
        $dob = '2024-01-01';
        $measDate = '2024-06-01';
        $res = $this->service->calculateBBU(-5.0, $dob, $measDate, 'L');

        $this->assertNull($res['zscore']);
    }

    /** Test 13: Boundary 24 months */
    public function test_13_boundary_24_months(): void
    {
        $dob = '2022-01-01';
        $measDateBefore = '2023-12-31'; // < 731 days
        $measDateAfter = '2024-01-02'; // >= 731 days

        $resBefore = $this->service->calculateBBTB(12.0, 85.0, $dob, $measDateBefore, 'L', 'terlentang');
        $resAfter = $this->service->calculateBBTB(12.0, 85.0, $dob, $measDateAfter, 'L', 'berdiri');

        $this->assertEquals('WFL', $resBefore['indicator_used']);
        $this->assertEquals('WFH', $resAfter['indicator_used']);
    }

    /** Test 14: Boundary 60 months */
    public function test_14_boundary_60_months(): void
    {
        $dob = '2019-01-01';
        $measDate60 = '2023-12-31'; // Exactly ~60 months
        $measDate61 = '2024-03-01'; // > 60 months

        $res60 = $this->service->calculateBBU(18.0, $dob, $measDate60, 'L');
        $res61 = $this->service->calculateBBU(18.0, $dob, $measDate61, 'L');

        $this->assertNotNull($res60['zscore']);
        $this->assertNull($res61['zscore']);
        $this->assertEquals('out_of_reference_range', $res61['status']);
    }
}
