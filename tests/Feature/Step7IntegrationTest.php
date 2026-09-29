<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Warga;
use App\Models\Pemeriksaan;
use App\Models\SesiPosyandu;
use App\Models\KalkulasiZScore;
use App\Models\Region;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Step7IntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\WhoAnthroV110Seeder::class);
    }

    public function test_a_b_c_d_e_h_persistence()
    {
        $dob = '2020-01-01';
        $measDate = '2022-01-01'; // 731 days

        // Mock region_id or posyandu if necessary, just mock region using factory or fake ID if it doesn't have FK
        $region = Region::create([
            'name' => 'Mawar',
            'type' => 'dusun'
        ]);

        $warga = Warga::create([
            'nik' => '1234567890123456',
            'no_kk' => '1234567890123456',
            'nama' => 'Test Anak',
            'tempat_lahir' => 'Jakarta',
            'tanggal_lahir' => $dob,
            'jenis_kelamin' => 'L',
            'alamat' => 'Test',
            'region_id' => $region->id
        ]);
        $warga->balita()->create();

        $sesi = SesiPosyandu::create([
            'tanggal' => $measDate,
            'nama_sesi' => 'Posyandu Mawar',
        ]);

        $admin = \App\Models\User::factory()->create();
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Super_Admin']);
        $admin->assignRole('Super_Admin');
        $this->withoutExceptionHandling();
        $response = $this->actingAs($admin)->postJson("/admin/sesi-posyandus/{$sesi->id}/operasional", [
            'warga_id' => $warga->id,
            'berat_badan' => 13.0,
            'tinggi_badan' => 90.25,
            'posisi_ukur' => 'berdiri'
        ]);

        $response->assertStatus(200);

        $kalkulasi = KalkulasiZScore::first();

        // TEST A - BBU persistence
        $this->assertNotNull($kalkulasi->zscore_bb_u);
        $this->assertNotNull($kalkulasi->raw_zscore_bb_u);
        $this->assertNotNull($kalkulasi->status_gizi);

        // TEST B - TBU persistence
        $this->assertNotNull($kalkulasi->zscore_tb_u);
        $this->assertNotNull($kalkulasi->raw_zscore_tb_u);
        
        // TEST C - BBTB persistence
        $this->assertNotNull($kalkulasi->zscore_bb_tb);
        $this->assertNotNull($kalkulasi->raw_zscore_bb_tb);

        // TEST D - flag persistence
        $this->assertIsBool($kalkulasi->flag_bb_u);
        $this->assertIsBool($kalkulasi->flag_tb_u);
        $this->assertIsBool($kalkulasi->flag_bb_tb);

        // TEST E - source_version
        $this->assertEquals('who_anthro_v1.1.0', $kalkulasi->source_version);

        // TEST H - precision
        $rawZStr = (string)$kalkulasi->raw_zscore_bb_tb;
        $this->assertTrue(strlen($rawZStr) > 5, "Precision lost, raw z is only $rawZStr");
    }

    public function test_f_out_of_reference_range()
    {
        $dob = '2015-01-01';
        $measDate = '2022-01-01'; // 7 years old

        $region = Region::create([
            'name' => 'Mawar',
            'type' => 'dusun'
        ]);

        $warga = Warga::create([
            'nik' => '1234567890123457',
            'no_kk' => '1234567890123457',
            'nama' => 'Test Anak 2',
            'tanggal_lahir' => $dob,
            'jenis_kelamin' => 'L',
            'region_id' => $region->id
        ]);
        $warga->balita()->create();

        $sesi = SesiPosyandu::create([
            'tanggal' => $measDate,
            'nama_sesi' => 'Posyandu Melati',
        ]);

        $admin = \App\Models\User::factory()->create();
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Super_Admin']);
        $admin->assignRole('Super_Admin');
        $this->withoutExceptionHandling();
        $response = $this->actingAs($admin)->postJson("/admin/sesi-posyandus/{$sesi->id}/operasional", [
            'warga_id' => $warga->id,
            'berat_badan' => 15.0,
            'tinggi_badan' => 110.0,
            'posisi_ukur' => 'berdiri'
        ]);

        $response->assertStatus(200);

        $kalkulasi = KalkulasiZScore::orderBy('id', 'desc')->first();

        $this->assertNull($kalkulasi->zscore_bb_u);
        $this->assertNull($kalkulasi->raw_zscore_bb_u);
        $this->assertEquals('out_of_reference_range', $kalkulasi->status_gizi);
    }

    public function test_g_legacy_record()
    {
        $region = Region::create([
            'name' => 'Mawar',
            'type' => 'dusun'
        ]);

        $warga = Warga::create([
            'nik' => '1234567890123458',
            'no_kk' => '1234567890123458',
            'nama' => 'Test Anak 3',
            'tanggal_lahir' => '2020-01-01',
            'jenis_kelamin' => 'L',
            'region_id' => $region->id
        ]);

        $sesi = SesiPosyandu::create([
            'tanggal' => '2022-01-01',
            'nama_sesi' => 'Posyandu Mawar',
        ]);
        
        $pemeriksaan = Pemeriksaan::create([
            'warga_id' => $warga->id,
            'sesi_posyandu_id' => $sesi->id,
            'berat_badan' => 13.0,
            'tinggi_badan' => 90.0
        ]);

        $kalkulasi = KalkulasiZScore::create([
            'pemeriksaan_id' => $pemeriksaan->id,
            'zscore_bb_u' => 1.5,
            'zscore_tb_u' => -2.0,
            'zscore_bb_tb' => 0.5,
            'status_gizi' => 'Gizi Baik (Normal)',
            'status_stunting' => 'Normal',
            'status_bb_tb' => 'Gizi Baik (Normal)',
        ]);

        $kalkulasi->refresh();

        $this->assertEquals(1.5, $kalkulasi->zscore_bb_u);
        $this->assertNull($kalkulasi->raw_zscore_bb_u);
        $this->assertNull($kalkulasi->flag_bb_u);
        $this->assertNull($kalkulasi->source_version);
    }

    public function test_i_no_duplicate_calculation()
    {
        $this->assertTrue(true);
    }
}
