<?php

namespace Tests\Feature;

use App\Models\KalkulasiZScore;
use App\Models\Pemeriksaan;
use App\Models\SesiPosyandu;
use App\Models\User;
use App\Models\Warga;
use App\Models\Balita;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TindakLanjutEwsTest extends TestCase
{
    use RefreshDatabase;

    protected $bidan;
    protected $pemeriksaan;

    protected function setUp(): void
    {
        parent::setUp();

        \Spatie\Permission\Models\Role::create(['name' => 'Puskesmas']);

        $this->bidan = User::factory()->create();
        $this->bidan->assignRole('Puskesmas');

        $region = \App\Models\Region::create(['name' => 'Test Region', 'type' => 'rt']);
        $sesi = SesiPosyandu::create(['status' => 'aktif', 'tanggal' => today(), 'nama_sesi' => 'Sesi 1']);
        
        $warga = Warga::create(['tanggal_lahir' => now()->subMonths(12), 'jenis_kelamin' => 'L', 'nama' => 'Test Warga', 'nik' => '1234567890123456', 'alamat' => 'Test', 'region_id' => $region->id]);
        Balita::create(['warga_id' => $warga->id]);

        $this->pemeriksaan = Pemeriksaan::create([
            'warga_id' => $warga->id,
            'sesi_posyandu_id' => $sesi->id,
            'is_validated' => true,
            'berat_badan' => 10,
            'tinggi_badan' => 80,
            'tipe_kohort' => 'balita'
        ]);

        KalkulasiZScore::create([
            'pemeriksaan_id' => $this->pemeriksaan->id,
            'zscore_bb_u' => -3,
            'zscore_tb_u' => -3,
            'zscore_bb_tb' => -3,
            'status_gizi' => 'Gizi Buruk',
            'status_stunting' => 'Sangat Pendek',
            'status_bb_tb' => 'Sangat Kurus',
            'ews_status' => 'bahaya',
            'ews_message' => 'Rujuk segera'
        ]);
    }

    public function test_bidan_can_open_tindak_lanjut_form_for_valid_pemeriksaan()
    {
        $response = $this->actingAs($this->bidan)
            ->get(route('bidan.pemeriksaan.tindak-lanjut', $this->pemeriksaan->id));

        $response->assertStatus(200);
        $response->assertSee('Tindak Lanjut EWS');
        $response->assertSee($this->pemeriksaan->warga->nama);
    }

    public function test_bidan_can_save_tindak_lanjut()
    {
        $response = $this->actingAs($this->bidan)
            ->patch(route('bidan.pemeriksaan.tindak-lanjut.update', $this->pemeriksaan->id), [
                'tindakan' => 'Dirujuk ke RSUD',
                'catatan' => 'Kondisi lemah'
            ]);

        $response->assertRedirect(route('bidan.dashboard'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('pemeriksaan', [
            'id' => $this->pemeriksaan->id,
            'tindakan' => 'Dirujuk ke RSUD',
            'catatan' => 'Kondisi lemah'
        ]);
    }

    public function test_pemeriksaan_lama_tetap_satu_record()
    {
        $countBefore = Pemeriksaan::count();

        $this->actingAs($this->bidan)
            ->patch(route('bidan.pemeriksaan.tindak-lanjut.update', $this->pemeriksaan->id), [
                'tindakan' => 'Dirujuk ke RSUD'
            ]);

        $this->assertEquals($countBefore, Pemeriksaan::count());
    }

    public function test_kalkulasi_zscore_tetap_satu_record()
    {
        $countBefore = KalkulasiZScore::count();

        $this->actingAs($this->bidan)
            ->patch(route('bidan.pemeriksaan.tindak-lanjut.update', $this->pemeriksaan->id), [
                'tindakan' => 'Dirujuk ke RSUD'
            ]);

        $this->assertEquals($countBefore, KalkulasiZScore::count());
    }

    public function test_all_zscores_remain_the_same()
    {
        $zscoreBefore = KalkulasiZScore::where('pemeriksaan_id', $this->pemeriksaan->id)->first();

        $this->actingAs($this->bidan)
            ->patch(route('bidan.pemeriksaan.tindak-lanjut.update', $this->pemeriksaan->id), [
                'tindakan' => 'Dirujuk ke RSUD',
                'berat_badan' => 999, // Should be ignored
                'tinggi_badan' => 999  // Should be ignored
            ]);

        $zscoreAfter = KalkulasiZScore::where('pemeriksaan_id', $this->pemeriksaan->id)->first();

        $this->assertEquals($zscoreBefore->zscore_bb_u, $zscoreAfter->zscore_bb_u);
        $this->assertEquals($zscoreBefore->zscore_tb_u, $zscoreAfter->zscore_tb_u);
        $this->assertEquals($zscoreBefore->status_gizi, $zscoreAfter->status_gizi);
    }

    public function test_bidan_cannot_access_unvalidated_pemeriksaan()
    {
        $sesiBaru = SesiPosyandu::create(['status' => 'aktif', 'tanggal' => today()->subDay(), 'nama_sesi' => 'Sesi 2']);
        $unvalidated = Pemeriksaan::create([
            'warga_id' => $this->pemeriksaan->warga_id,
            'sesi_posyandu_id' => $sesiBaru->id,
            'is_validated' => false,
            'tipe_kohort' => 'balita'
        ]);

        $response = $this->actingAs($this->bidan)
            ->get(route('bidan.pemeriksaan.tindak-lanjut', $unvalidated->id));

        $response->assertStatus(403);
    }

    public function test_tindak_lanjut_cannot_change_antropometri()
    {
        $this->actingAs($this->bidan)
            ->patch(route('bidan.pemeriksaan.tindak-lanjut.update', $this->pemeriksaan->id), [
                'tindakan' => 'Dirujuk',
                'berat_badan' => 50,
                'tinggi_badan' => 150
            ]);

        $this->assertDatabaseHas('pemeriksaan', [
            'id' => $this->pemeriksaan->id,
            'berat_badan' => 10, // Tetap sama
            'tinggi_badan' => 80 // Tetap sama
        ]);
    }

    public function test_tindakan_wajib_diisi_sebelum_alert_dinyatakan_selesai()
    {
        $response = $this->actingAs($this->bidan)
            ->patch(route('bidan.pemeriksaan.tindak-lanjut.update', $this->pemeriksaan->id), [
                'tindakan' => '',
                'catatan' => 'Balita perlu pemantauan lanjutan',
            ]);

        $response->assertSessionHasErrors('tindakan');

        $kalkulasi = $this->pemeriksaan->kalkulasiZscore()->first();

        $this->assertNotEquals('resolved', $kalkulasi->fresh()->alert_status);
        $this->assertNull($kalkulasi->fresh()->resolved_at);
        $this->assertNull($kalkulasi->fresh()->resolved_by);
    }

}
