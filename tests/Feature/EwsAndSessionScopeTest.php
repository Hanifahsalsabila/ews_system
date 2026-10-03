<?php

namespace Tests\Feature;

use App\Models\KalkulasiZScore;
use App\Models\Pemeriksaan;
use App\Models\Region;
use App\Models\SesiPosyandu;
use App\Models\User;
use App\Models\Warga;
use App\Models\Balita;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EwsAndSessionScopeTest extends TestCase
{
    use RefreshDatabase;

    protected $bidan;
    protected $region;
    protected $warga;
    protected $balita;

    protected function setUp(): void
    {
        parent::setUp();
        
        \Spatie\Permission\Models\Role::create(['name' => 'Puskesmas']);
        
        $this->bidan = User::factory()->create();
        $this->bidan->assignRole('Puskesmas');

        $this->region = Region::create(['name' => 'Test', 'type' => 'dusun']);
        $this->warga = Warga::create([
            'nama' => 'Test Warga',
            'nik' => '1234567890123456',
            'tanggal_lahir' => today()->subMonths(12),
            'jenis_kelamin' => 'L',
            'alamat' => 'Alamat',
            'region_id' => $this->region->id
        ]);
        $this->balita = Balita::create(['warga_id' => $this->warga->id]);
    }

    public function test_ews_unvalidated_tidak_muncul()
    {
        $sesi = SesiPosyandu::create(['status' => 'aktif', 'tanggal' => today(), 'nama_sesi' => 'Sesi 1']);
        
        $pem = Pemeriksaan::create([
            'warga_id' => $this->warga->id,
            'sesi_posyandu_id' => $sesi->id,
            'is_validated' => false, // Unvalidated
            'tipe_kohort' => 'balita'
        ]);

        KalkulasiZScore::create([
            'pemeriksaan_id' => $pem->id,
            'ews_status' => 'bahaya'
        ]);

        $response = $this->actingAs($this->bidan)->get(route('bidan.dashboard'));
        $response->assertStatus(200);
        $response->assertDontSee($this->warga->nama);
    }

    public function test_ews_validated_muncul()
    {
        $sesi = SesiPosyandu::create(['status' => 'aktif', 'tanggal' => today(), 'nama_sesi' => 'Sesi 1']);
        
        $pem = Pemeriksaan::create([
            'warga_id' => $this->warga->id,
            'sesi_posyandu_id' => $sesi->id,
            'is_validated' => true, // Validated
            'tipe_kohort' => 'balita'
        ]);

        KalkulasiZScore::create([
            'pemeriksaan_id' => $pem->id,
            'ews_status' => 'bahaya',
            'ews_message' => 'Test Alert'
        ]);

        $response = $this->actingAs($this->bidan)->get(route('bidan.dashboard'));
        $response->assertStatus(200);
        $response->assertSee($this->warga->nama);
        $response->assertSee('Test Alert');
    }

    public function test_same_day_different_session_allows_input()
    {
        $sesi1 = SesiPosyandu::create(['status' => 'selesai', 'tanggal' => today(), 'nama_sesi' => 'Sesi 1']);
        $sesi2 = SesiPosyandu::create(['status' => 'aktif', 'tanggal' => today(), 'nama_sesi' => 'Sesi 2']);

        // Warga is validated in Sesi 1
        Pemeriksaan::create([
            'warga_id' => $this->warga->id,
            'sesi_posyandu_id' => $sesi1->id,
            'is_validated' => true,
            'tipe_kohort' => 'balita'
        ]);

        // Warga is unvalidated in Sesi 2
        $pem2 = Pemeriksaan::create([
            'warga_id' => $this->warga->id,
            'sesi_posyandu_id' => $sesi2->id,
            'is_validated' => false,
            'tipe_kohort' => 'balita'
        ]);

        $response = $this->actingAs($this->bidan)->get(route('bidan.pemeriksaan.balita'));
        $response->assertStatus(200);
        
        // The balita page passes $sudahDiperiksa to the view. We can check if it's empty.
        $sudahDiperiksa = $response->viewData('sudahDiperiksa');
        
        // Note: The collection might be empty if it only contains validated items for sesi2.
        // And indeed, there are no validated items for sesi2!
        $this->assertFalse(collect($sudahDiperiksa)->contains($this->warga->id));
    }

    public function test_same_session_duplicate_is_disabled()
    {
        $sesi = SesiPosyandu::create(['status' => 'aktif', 'tanggal' => today(), 'nama_sesi' => 'Sesi 1']);

        Pemeriksaan::create([
            'warga_id' => $this->warga->id,
            'sesi_posyandu_id' => $sesi->id,
            'is_validated' => true,
            'tipe_kohort' => 'balita'
        ]);

        $response = $this->actingAs($this->bidan)->get(route('bidan.pemeriksaan.balita'));
        $response->assertStatus(200);
        
        $sudahDiperiksa = $response->viewData('sudahDiperiksa');
        $this->assertTrue(collect($sudahDiperiksa)->contains($this->warga->id));
    }
}
