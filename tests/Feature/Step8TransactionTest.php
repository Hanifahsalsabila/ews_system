<?php

namespace Tests\Feature;

use App\Models\KalkulasiZScore;
use App\Models\Pemeriksaan;
use App\Models\SesiPosyandu;
use App\Models\User;
use App\Models\Warga;
use App\Models\Balita;
use App\Models\Region;
use App\Services\WhoZscoreService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Mockery;

class Step8TransactionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->artisan('db:seed', ['--class' => 'RoleSeeder']);
    }

    private function createDependencies($nikSuffix = '1234')
    {
        $sesi = SesiPosyandu::create(['tanggal' => today(), 'status' => 'aktif', 'nama_sesi' => 'Sesi', 'lokasi' => 'Pos', 'waktu_mulai' => '08:00', 'waktu_selesai' => '12:00']);
        $region = Region::create(['name' => 'Test Region', 'type' => 'dusun']);
        $warga = Warga::create(['nama' => 'Test', 'nik' => '123456789012' . $nikSuffix, 'tanggal_lahir' => today()->subMonths(12), 'jenis_kelamin' => 'L', 'alamat' => 'A', 'region_id' => $region->id]);
        return [$sesi, $region, $warga];
    }

    public function test_transaction_success_saves_all_data()
    {
        $kader = User::factory()->create();
        $kader->assignRole('Kader_Posyandu');

        [$sesi, $region, $warga] = $this->createDependencies('0001');
        \App\Models\Absensi::create(['warga_id' => $warga->id, 'sesi_posyandu_id' => $sesi->id, 'waktu_hadir' => now()]);
        Balita::create(['warga_id' => $warga->id, 'nama_orang_tua' => 'Ibu', 'berat_lahir' => 3.0, 'panjang_lahir' => 50.0]);

        $response = $this->actingAs($kader)->post(route('kader.pengukuran.store', $warga->id), [
            'berat_badan' => 10.5,
            'tinggi_badan' => 75.0,
            'posisi_ukur' => 'berdiri'
        ]);

        $response->assertSessionHas('success');
        
        $pemeriksaan = Pemeriksaan::where('warga_id', $warga->id)->first();
        $this->assertNotNull($pemeriksaan);
        
        $kalkulasi = KalkulasiZScore::where('pemeriksaan_id', $pemeriksaan->id)->first();
        $this->assertNotNull($kalkulasi);
    }

    public function test_calculation_failure_rolls_back_everything()
    {
        $kader = User::factory()->create();
        $kader->assignRole('Kader_Posyandu');

        [$sesi, $region, $warga] = $this->createDependencies('0002');
        \App\Models\Absensi::create(['warga_id' => $warga->id, 'sesi_posyandu_id' => $sesi->id, 'waktu_hadir' => now()]);
        Balita::create(['warga_id' => $warga->id, 'nama_orang_tua' => 'Ibu', 'berat_lahir' => 3.0, 'panjang_lahir' => 50.0]);

        // Mock WhoZscoreService to throw an exception
        $this->mock(WhoZscoreService::class, function ($mock) {
            $mock->shouldReceive('calculateBBU')->andThrow(new \Exception("Simulated calculation error"));
        });

        $response = $this->actingAs($kader)->from(route('kader.pengukuran.create', $warga->id))->post(route('kader.pengukuran.store', $warga->id), [
            'berat_badan' => 10.5,
            'tinggi_badan' => 75.0,
            'posisi_ukur' => 'berdiri'
        ]);

        $response->assertSessionHas('error');
        
        // Assert rollback: Pemeriksaan should NOT exist!
        $this->assertDatabaseMissing('pemeriksaan', [
            'warga_id' => $warga->id
        ]);
    }

    public function test_duplicate_pemeriksaan_id_is_prevented_by_unique_constraint()
    {
        $kader = User::factory()->create();
        
        [$sesi, $region, $warga] = $this->createDependencies('0003');
        $pemeriksaan = Pemeriksaan::create([
            'warga_id' => $warga->id,
            'sesi_posyandu_id' => $sesi->id,
            'tipe_kohort' => 'balita',
            'berat_badan' => 10,
            'tinggi_badan' => 75,
            'posisi_ukur' => 'berdiri',
            'is_validated' => 1,
            'petugas_id' => $kader->id
        ]);

        // Create first kalkulasi
        KalkulasiZScore::create([
            'pemeriksaan_id' => $pemeriksaan->id,
            'status_gizi' => 'Baik',
            'status_stunting' => 'Normal',
            'status_bb_tb' => 'Normal',
            'ews_status' => 'normal',
            'ews_message' => 'test'
        ]);

        // Trying to create a second one should throw a query exception
        $this->expectException(\Illuminate\Database\QueryException::class);
        $this->expectExceptionMessageMatches('/Integrity constraint violation/i');
        
        KalkulasiZScore::create([
            'pemeriksaan_id' => $pemeriksaan->id, // DUPLICATE ID
            'status_gizi' => 'Buruk',
            'status_stunting' => 'Normal',
            'status_bb_tb' => 'Normal',
            'ews_status' => 'normal',
            'ews_message' => 'test'
        ]);
    }

    public function test_existing_legacy_records_remain_intact()
    {
        $kader = User::factory()->create();
        
        [$sesi, $region, $warga] = $this->createDependencies('0004');
        $pemeriksaan = Pemeriksaan::create([
            'warga_id' => $warga->id,
            'sesi_posyandu_id' => $sesi->id,
            'tipe_kohort' => 'balita',
            'berat_badan' => 10,
            'tinggi_badan' => 75,
            'posisi_ukur' => 'berdiri',
            'is_validated' => 1,
            'petugas_id' => $kader->id
        ]);
        
        // Simulating a legacy record where source_version and raw fields are null
        $kalkulasi = KalkulasiZScore::create([
            'pemeriksaan_id' => $pemeriksaan->id,
            'zscore_bb_u' => -1.5,
            'status_gizi' => 'Baik',
            'status_stunting' => 'Normal',
            'status_bb_tb' => 'Normal',
            'ews_status' => 'normal',
            'ews_message' => 'Legacy message',
        ]);

        $fetched = KalkulasiZScore::find($kalkulasi->id);
        
        $this->assertNull($fetched->source_version);
        $this->assertNull($fetched->raw_zscore_bb_u);
        $this->assertEquals(-1.5, $fetched->zscore_bb_u);
    }

    public function test_authorization_warga_lintas_posyandu_ditolak()
    {
        $kader = User::factory()->create();
        $kader->assignRole('Kader_Posyandu');

        [$sesi, $region, $warga] = $this->createDependencies('0005');
        // DO NOT add Warga to Absensi for this session.

        $response = $this->actingAs($kader)->from(route('kader.pengukuran.index'))->post(route('kader.pengukuran.store', $warga->id), [
            'berat_badan' => 10.5,
            'tinggi_badan' => 75.0,
            'posisi_ukur' => 'berdiri'
        ]);

        $response->assertSessionHas('error', 'Warga tidak terdaftar pada sesi posyandu ini.');
        $this->assertDatabaseMissing('pemeriksaan', [
            'warga_id' => $warga->id
        ]);
    }

    public function test_duplicate_pemeriksaan_ditolak()
    {
        $kader = User::factory()->create();
        $kader->assignRole('Kader_Posyandu');

        [$sesi, $region, $warga] = $this->createDependencies('0006');
        \App\Models\Absensi::create([
            'warga_id' => $warga->id,
            'sesi_posyandu_id' => $sesi->id,
            'waktu_hadir' => now()
        ]);
        
        Pemeriksaan::create([
            'warga_id' => $warga->id,
            'sesi_posyandu_id' => $sesi->id,
            'tipe_kohort' => 'balita',
            'berat_badan' => 10,
            'tinggi_badan' => 75,
            'posisi_ukur' => 'berdiri',
            'is_validated' => 1,
            'petugas_id' => $kader->id
        ]);

        $response = $this->actingAs($kader)->from(route('kader.pengukuran.index'))->post(route('kader.pengukuran.store', $warga->id), [
            'berat_badan' => 11.5,
            'tinggi_badan' => 76.0,
            'posisi_ukur' => 'berdiri'
        ]);

        $response->assertSessionHas('error', 'Warga sudah melakukan pengukuran pada sesi ini.');
        
        // Ensure second record is not created
        $count = Pemeriksaan::where('warga_id', $warga->id)->where('sesi_posyandu_id', $sesi->id)->count();
        $this->assertEquals(1, $count);
    }
}


