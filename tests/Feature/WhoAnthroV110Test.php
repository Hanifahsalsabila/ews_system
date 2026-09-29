<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class WhoAnthroV110Test extends TestCase
{
    /**
     * A basic feature test example.
     */
    use \Illuminate\Foundation\Testing\RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Run both seeders to test coexistence
        $this->seed(\Database\Seeders\WhoZscoreLmsSeeder::class);
        $this->seed(\Database\Seeders\WhoAnthroV110Seeder::class);
    }

    public function test_canonical_files_checksum()
    {
        $basePath = database_path('data/who/anthro/v1.1.0');
        
        $expected = [
            'weianthro.txt' => 'BC15A6A623DD1D5BEAEED1497666332AA54BC4CCD15FF9658C487D79694AB77B',
            'lenanthro.txt' => '709F7A11881451DAF7820F022D363D5BDB93746B5361D6BD9218AF6FF838E0C2',
            'wflanthro.txt' => 'AD470CF41B147BD2A16026E57FD17968DF7BF746B9F0BF2FF85DF95239643778',
            'wfhanthro.txt' => '0050D31041C2F7D4F8A34F27E8066FD73A24806835B9E4A0DE1A7EE46D54D582'
        ];

        foreach ($expected as $file => $hash) {
            $path = "$basePath/$file";
            $this->assertFileExists($path);
            $this->assertEquals(strtolower($hash), strtolower(hash_file('sha256', $path)), "Checksum mismatch for $file");
        }
    }

    public function test_schema_has_source_version_and_lorh()
    {
        $this->assertTrue(\Illuminate\Support\Facades\Schema::hasColumn('who_zscore_lms', 'source_version'));
        $this->assertTrue(\Illuminate\Support\Facades\Schema::hasColumn('who_zscore_lms', 'lorh'));
    }

    public function test_legacy_preservation_and_source_version_separation()
    {
        $legacyCount = \Illuminate\Support\Facades\DB::table('who_zscore_lms')->where('source_version', 'legacy')->count();
        $this->assertGreaterThan(0, $legacyCount, "Legacy data should be preserved");

        $v1Count = \Illuminate\Support\Facades\DB::table('who_zscore_lms')->where('source_version', 'who_anthro_v1.1.0')->count();
        $this->assertGreaterThan(0, $v1Count, "v1.1.0 data should be seeded");
    }

    public function test_waz_haz_row_count_and_range()
    {
        $indicators = ['WAZ', 'HAZ'];
        foreach ($indicators as $indicator) {
            foreach (['L', 'P'] as $gender) {
                $count = \Illuminate\Support\Facades\DB::table('who_zscore_lms')
                    ->where('source_version', 'who_anthro_v1.1.0')
                    ->where('indicator', $indicator)
                    ->where('gender', $gender)
                    ->count();
                $this->assertEquals(1827, $count, "Full daily data 0-1826 for $indicator $gender");
                
                $maxDay = \Illuminate\Support\Facades\DB::table('who_zscore_lms')
                    ->where('source_version', 'who_anthro_v1.1.0')
                    ->where('indicator', $indicator)
                    ->where('gender', $gender)
                    ->max('ref_value');
                $this->assertEquals(1826, $maxDay, "Max day must be 1826");
            }
        }
    }

    public function test_wfl_wfh_spacing_and_range()
    {
        foreach (['L', 'P'] as $gender) {
            $wflCount = \Illuminate\Support\Facades\DB::table('who_zscore_lms')
                ->where('source_version', 'who_anthro_v1.1.0')
                ->where('indicator', 'WFL')
                ->where('gender', $gender)
                ->count();
            $this->assertEquals(651, $wflCount, "WFL $gender should have 651 points");

            $wfhCount = \Illuminate\Support\Facades\DB::table('who_zscore_lms')
                ->where('source_version', 'who_anthro_v1.1.0')
                ->where('indicator', 'WFH')
                ->where('gender', $gender)
                ->count();
            $this->assertEquals(551, $wfhCount, "WFH $gender should have 551 points");
        }
    }

    public function test_precision_values()
    {
        // WAZ male day 0
        $wazM0 = \Illuminate\Support\Facades\DB::table('who_zscore_lms')->where('source_version', 'who_anthro_v1.1.0')->where('indicator', 'WAZ')->where('gender', 'L')->where('ref_value', 0)->first();
        $this->assertEquals(0.3487, $wazM0->l);
        $this->assertEquals(3.3464, $wazM0->m);
        $this->assertEquals(0.14602, $wazM0->s);

        // WAZ female day 0
        $wazF0 = \Illuminate\Support\Facades\DB::table('who_zscore_lms')->where('source_version', 'who_anthro_v1.1.0')->where('indicator', 'WAZ')->where('gender', 'P')->where('ref_value', 0)->first();
        $this->assertEquals(0.3809, $wazF0->l);
        $this->assertEquals(3.2322, $wazF0->m);
        $this->assertEquals(0.14171, $wazF0->s);

        // WAZ male day 1826
        $wazM1826 = \Illuminate\Support\Facades\DB::table('who_zscore_lms')->where('source_version', 'who_anthro_v1.1.0')->where('indicator', 'WAZ')->where('gender', 'L')->where('ref_value', 1826)->first();
        $this->assertEquals(-0.1506, $wazM1826->l);
        $this->assertEquals(18.3352, $wazM1826->m);
        $this->assertEquals(0.13517, $wazM1826->s);

        // HAZ male day 0
        $hazM0 = \Illuminate\Support\Facades\DB::table('who_zscore_lms')->where('source_version', 'who_anthro_v1.1.0')->where('indicator', 'HAZ')->where('gender', 'L')->where('ref_value', 0)->first();
        $this->assertEquals(1, $hazM0->l);
        $this->assertEquals(49.8842, $hazM0->m);
        $this->assertEquals(0.03795, $hazM0->s);
        $this->assertEquals('L', $hazM0->lorh); // From lenanthro 'loh'

        // WFL male 68.0
        $wflM68 = \Illuminate\Support\Facades\DB::table('who_zscore_lms')->where('source_version', 'who_anthro_v1.1.0')->where('indicator', 'WFL')->where('gender', 'L')->where('ref_value', 68.0)->first();
        $this->assertEquals(-0.3521, $wflM68->l);
        $this->assertEquals(7.9674, $wflM68->m);
        $this->assertEquals(0.08214, $wflM68->s);
        $this->assertEquals('L', $wflM68->lorh); // From wflanthro 'lorh'

        // WFH male 90.0
        $wfhM90 = \Illuminate\Support\Facades\DB::table('who_zscore_lms')->where('source_version', 'who_anthro_v1.1.0')->where('indicator', 'WFH')->where('gender', 'L')->where('ref_value', 90.0)->first();
        $this->assertEquals(-0.3521, $wfhM90->l);
        $this->assertEquals(12.8864, $wfhM90->m);
        $this->assertEquals(0.08032, $wfhM90->s);
        $this->assertEquals('H', $wfhM90->lorh); // From wfhanthro 'lorh'
    }
}
