<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Services\WhoZscoreService;
use Carbon\Carbon;
use Database\Seeders\WhoAnthroV110Seeder;

class WhoZscoreEngineTest extends TestCase
{
    use RefreshDatabase;

    protected WhoZscoreService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(WhoAnthroV110Seeder::class);
        $this->service = new WhoZscoreService();
    }

    private function getTestDob(float $ageInDays)
    {
        // Untuk mengatasi fractional days, kita akan menggunakan diffInRealHours
        // Measurement Date = Now
        // DOB = Now - ageInDays
        $meas = Carbon::now()->startOfDay(); // "00:00:00"
        
        $hours = $ageInDays * 24.0;
        
        // Coba exact round minutes
        $mins = (int) round($hours * 60);
        $dob = $meas->copy()->subMinutes($mins);

        return [$dob->format('Y-m-d H:i:s'), $meas->format('Y-m-d H:i:s')];
    }

    public function test_waz_golden_vectors()
    {
        $vectors = [
            ['gender' => 'L', 'age' => 0, 'val' => 3.3, 'raw_z' => -0.0954, 'adj_z' => -0.0954, 'final' => -0.1, 'flag' => false],
            ['gender' => 'P', 'age' => 0, 'val' => 3.2, 'raw_z' => -0.0705, 'adj_z' => -0.0705, 'final' => -0.07, 'flag' => false],
            ['gender' => 'L', 'age' => 30, 'val' => 4.5, 'raw_z' => 0.0792, 'adj_z' => 0.0792, 'final' => 0.08, 'flag' => false],
            ['gender' => 'P', 'age' => 30, 'val' => 4.2, 'raw_z' => 0.0494, 'adj_z' => 0.0494, 'final' => 0.05, 'flag' => false],
            ['gender' => 'L', 'age' => 365, 'val' => 9.6, 'raw_z' => -0.0437, 'adj_z' => -0.0437, 'final' => -0.04, 'flag' => false],
            ['gender' => 'P', 'age' => 365, 'val' => 8.9, 'raw_z' => -0.0422, 'adj_z' => -0.0422, 'final' => -0.04, 'flag' => false],
            ['gender' => 'L', 'age' => 730, 'val' => 12.2, 'raw_z' => 0.0372, 'adj_z' => 0.0372, 'final' => 0.04, 'flag' => false],
            ['gender' => 'P', 'age' => 730, 'val' => 11.5, 'raw_z' => 0.0182, 'adj_z' => 0.0182, 'final' => 0.02, 'flag' => false],
            ['gender' => 'L', 'age' => 731, 'val' => 12.2, 'raw_z' => 0.0325, 'adj_z' => 0.0325, 'final' => 0.03, 'flag' => false],
            ['gender' => 'P', 'age' => 731, 'val' => 11.5, 'raw_z' => 0.0134, 'adj_z' => 0.0134, 'final' => 0.01, 'flag' => false],
            ['gender' => 'L', 'age' => 1826, 'val' => 18.3, 'raw_z' => -0.0142, 'adj_z' => -0.0142, 'final' => -0.01, 'flag' => false],
            ['gender' => 'P', 'age' => 1826, 'val' => 18.2, 'raw_z' => -0.0066, 'adj_z' => -0.0066, 'final' => -0.01, 'flag' => false],
        ];

        foreach ($vectors as $vec) {
            [$dob, $meas] = $this->getTestDob($vec['age']);
            $res = $this->service->calculateBBU($vec['val'], $dob, $meas, $vec['gender']);
            
            $this->assertNotNull($res['zscore']);
            $this->assertEqualsWithDelta($vec['raw_z'], $res['raw_zscore'], 0.0001);
            $this->assertEquals($vec['final'], $res['zscore']);
            $this->assertEquals($vec['flag'], $res['flag']);
        }
    }

    public function test_haz_golden_vectors()
    {
        $vectors = [
            ['gender' => 'L', 'age' => 0, 'val' => 49.9, 'raw_z' => 0.0083, 'final' => 0.01, 'flag' => false],
            ['gender' => 'P', 'age' => 0, 'val' => 49.1, 'raw_z' => -0.0256, 'final' => -0.03, 'flag' => false],
            ['gender' => 'L', 'age' => 730, 'val' => 87.1, 'raw_z' => -0.2297, 'final' => -0.23, 'flag' => false],
            ['gender' => 'P', 'age' => 730, 'val' => 85.7, 'raw_z' => -0.2173, 'final' => -0.22, 'flag' => false],
            ['gender' => 'L', 'age' => 731, 'val' => 86.4, 'raw_z' => -0.2389, 'final' => -0.24, 'flag' => false],
            ['gender' => 'P', 'age' => 731, 'val' => 85.0, 'raw_z' => -0.2262, 'final' => -0.23, 'flag' => false],
            ['gender' => 'L', 'age' => 1826, 'val' => 110.0, 'raw_z' => 0.0088, 'final' => 0.01, 'flag' => false],
            ['gender' => 'P', 'age' => 1826, 'val' => 109.4, 'raw_z' => -0.0040, 'final' => 0.00, 'flag' => false],
        ];

        foreach ($vectors as $vec) {
            [$dob, $meas] = $this->getTestDob($vec['age']);
            $posisi = ($vec['age'] < 731) ? 'terlentang' : 'berdiri'; 
            
            $res = $this->service->calculateTBU($vec['val'], $dob, $meas, $vec['gender'], $posisi);
            
            $this->assertNotNull($res['zscore']);
            $this->assertEqualsWithDelta($vec['raw_z'], $res['raw_zscore'], 0.0001);
            $this->assertEquals($vec['final'], $res['zscore']);
            $this->assertEquals($vec['flag'], $res['flag']);
        }
    }

    public function test_wfl_wfh_golden_vectors()
    {
        $vectors = [
            // WHO Anthro v1.1.0 wflanthro.txt WFL male 68.0 cm
            ['ind' => 'WFL', 'gender' => 'L', 'height' => 68.0, 'val' => 8.0, 'raw_z' => 0.0497, 'final' => 0.05, 'flag' => false],
            // WFL interpolation at 68.25 cm between 68.2 and 68.3
            ['ind' => 'WFL', 'gender' => 'L', 'height' => 68.5, 'val' => 8.2, 'raw_z' => 0.1766, 'final' => 0.18, 'flag' => false],
            ['ind' => 'WFL', 'gender' => 'L', 'height' => 68.25, 'val' => 8.1, 'raw_z' => 0.1137, 'final' => 0.11, 'flag' => false],
            ['ind' => 'WFL', 'gender' => 'L', 'height' => 45.0, 'val' => 2.5, 'raw_z' => 0.2590, 'final' => 0.26, 'flag' => false],
            ['ind' => 'WFL', 'gender' => 'L', 'height' => 110.0, 'val' => 18.9, 'raw_z' => 0.3856, 'final' => 0.39, 'flag' => false],
            ['ind' => 'WFH', 'gender' => 'L', 'height' => 90.0, 'val' => 12.9, 'raw_z' => 0.0131, 'final' => 0.01, 'flag' => false],
            ['ind' => 'WFH', 'gender' => 'L', 'height' => 90.5, 'val' => 13.0, 'raw_z' => -0.0036, 'final' => 0.00, 'flag' => false],
            // WFH interpolation at 90.25 cm between 90.2 and 90.3
            ['ind' => 'WFH', 'gender' => 'L', 'height' => 90.25, 'val' => 13.0, 'raw_z' => 0.0526, 'final' => 0.05, 'flag' => false],
            ['ind' => 'WFH', 'gender' => 'L', 'height' => 65.0, 'val' => 7.3, 'raw_z' => -0.2199, 'final' => -0.22, 'flag' => false],
            ['ind' => 'WFH', 'gender' => 'L', 'height' => 120.0, 'val' => 22.8, 'raw_z' => 0.2094, 'final' => 0.21, 'flag' => false],
        ];

        foreach ($vectors as $vec) {
            $age = $vec['ind'] === 'WFL' ? 365 : 735;
            $posisi = $vec['ind'] === 'WFL' ? 'terlentang' : 'berdiri';
            
            [$dob, $meas] = $this->getTestDob($age);
            
            $res = $this->service->calculateBBTB($vec['val'], $vec['height'], $dob, $meas, $vec['gender'], $posisi);
            
            $this->assertNotNull($res['zscore']);
            $this->assertEquals($vec['ind'], $res['indicator_used']);
            $this->assertEqualsWithDelta($vec['raw_z'], $res['raw_zscore'], 0.0001);
            $this->assertEquals($vec['final'], $res['zscore']);
            $this->assertEquals($vec['flag'], $res['flag']);
        }
    }

    public function test_age_transition_continuous()
    {
        [$dob, $meas] = $this->getTestDob(730.49);
        $res = $this->service->calculateBBU(12.2, $dob, $meas, 'L');
        $this->assertEquals(0.04, $res['zscore']); 

        [$dob, $meas] = $this->getTestDob(730.50);
        $res = $this->service->calculateBBU(12.2, $dob, $meas, 'L');
        $this->assertEquals(0.03, $res['zscore']); 

        [$dob, $meas] = $this->getTestDob(731.0);
        $res = $this->service->calculateBBU(12.2, $dob, $meas, 'L');
        $this->assertEquals(0.03, $res['zscore']);
    }

    public function test_continuous_boundary_tests()
    {
        $validEdges = [1826.00, 1826.24];
        foreach ($validEdges as $edge) {
            [$dob, $meas] = $this->getTestDob($edge);
            $res = $this->service->calculateBBU(18.3, $dob, $meas, 'L');
            $this->assertNotNull($res['zscore']); 
        }

        $invalidEdges = [1826.25, 1826.26, 1826.49, 1826.50, 1827.00];
        foreach ($invalidEdges as $edge) {
            [$dob, $meas] = $this->getTestDob($edge);
            $res = $this->service->calculateBBU(18.3, $dob, $meas, 'L');
            $this->assertNull($res['zscore']); 
            $this->assertEquals('out_of_reference_range', $res['status']);
        }
    }

    public function test_bbtb_continuous_boundary_tests()
    {
        $validEdges = [1826.00, 1826.24];
        foreach ($validEdges as $edge) {
            [$dob, $meas] = $this->getTestDob($edge);
            $res = $this->service->calculateBBTB(18.3, 110.0, $dob, $meas, 'L');
            $this->assertNotNull($res['zscore']); 
        }

        $invalidEdges = [1826.25, 1826.26, 1826.49, 1826.50, 1827.00];
        foreach ($invalidEdges as $edge) {
            [$dob, $meas] = $this->getTestDob($edge);
            $res = $this->service->calculateBBTB(18.3, 110.0, $dob, $meas, 'L');
            $this->assertNull($res['zscore']); 
            $this->assertEquals('out_of_reference_range', $res['status']);
        }
    }

    public function test_invalid_range_wfl_wfh()
    {
        [$dob, $meas] = $this->getTestDob(365);
        $res = $this->service->calculateBBTB(2.0, 44.9, $dob, $meas, 'L', 'terlentang');
        $this->assertNull($res['zscore']);
        
        $res = $this->service->calculateBBTB(19.0, 110.1, $dob, $meas, 'L', 'terlentang');
        $this->assertNull($res['zscore']);

        [$dob, $meas] = $this->getTestDob(800);
        $res = $this->service->calculateBBTB(7.0, 64.9, $dob, $meas, 'L', 'berdiri');
        $this->assertNull($res['zscore']);

        $res = $this->service->calculateBBTB(23.0, 120.1, $dob, $meas, 'L', 'berdiri');
        $this->assertNull($res['zscore']);
    }

    public function test_measurement_correction()
    {
        [$dob, $meas] = $this->getTestDob(365);
        $res = $this->service->calculateTBU(68.0, $dob, $meas, 'L', 'berdiri');
        $this->assertEquals(68.7, $res['adjusted_height']);
        $this->assertEquals('LENGTH', $res['measurement_type']);

        [$dob, $meas] = $this->getTestDob(800);
        $res = $this->service->calculateTBU(90.0, $dob, $meas, 'L', 'terlentang');
        $this->assertEquals(89.3, $res['adjusted_height']);
        $this->assertEquals('HEIGHT', $res['measurement_type']);
    }

    public function test_extreme_z()
    {
        [$dob, $meas] = $this->getTestDob(365);
        
        $res = $this->service->calculateBBU(15.0, $dob, $meas, 'L');
        $this->assertEquals(4.0993, $res['raw_zscore']);
        $this->assertEquals(4.22, $res['zscore']);
        $this->assertFalse($res['flag']); 

        $res = $this->service->calculateBBU(4.0, $dob, $meas, 'L');
        $this->assertEquals(-7.8327, $res['raw_zscore']);
        $this->assertEquals(-6.59, $res['zscore']);
        $this->assertTrue($res['flag']); 

        $res = $this->service->calculateTBU(100.0, $dob, $meas, 'L', 'terlentang');
        $this->assertEquals(10.2111, $res['raw_zscore']);
        $this->assertEquals(10.21, $res['zscore']);
        $this->assertTrue($res['flag']); 
    }
}
