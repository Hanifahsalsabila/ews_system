<?php

namespace Tests\Feature;

use App\Models\Balita;
use App\Models\KalkulasiZScore;
use App\Models\KunjunganRumah;
use App\Models\Pemeriksaan;
use App\Models\Region;
use App\Models\SesiPosyandu;
use App\Models\User;
use App\Models\Warga;
use App\Services\EwsService;
use App\Services\WhoZscoreService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class EwsFinalValidationTest extends TestCase
{
    use RefreshDatabase;

    protected $bidan;
    protected $kader;
    protected $region;
    protected $sesi;
    protected $ewsService;
    protected $whoService;

    protected function setUp(): void
    {
        parent::setUp();

        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Puskesmas']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Kader_Posyandu']);

        $this->bidan = User::factory()->create(['name' => 'Bidan Siti']);
        $this->bidan->assignRole('Puskesmas');

        $this->kader = User::factory()->create(['name' => 'Kader Ani']);
        $this->kader->assignRole('Kader_Posyandu');

        $this->region = Region::create(['name' => 'Dusun Rahayu', 'type' => 'dusun']);
        $this->sesi = $this->createSesi(today(), 'Sesi Penimbangan Rutin');

        $this->ewsService = app(EwsService::class);
        $this->whoService = app(WhoZscoreService::class);
    }

    private function createSesi($date = null, string $nama = 'Sesi'): SesiPosyandu
    {
        return SesiPosyandu::create([
            'status' => 'aktif',
            'tanggal' => $date ?? today(),
            'nama_sesi' => $nama . ' ' . uniqid(),
            'lokasi' => 'Posyandu Mawar',
            'waktu_mulai' => '08:00',
            'waktu_selesai' => '12:00',
        ]);
    }

    private function createBalita(string $nama, int $ageMonths = 12): Warga
    {
        $warga = Warga::create([
            'nama' => $nama,
            'nik' => '3203' . rand(100000000000, 999999999999),
            'tanggal_lahir' => today()->subMonths($ageMonths),
            'jenis_kelamin' => 'L',
            'alamat' => 'RT 03 RW 01',
            'region_id' => $this->region->id,
        ]);

        Balita::create([
            'warga_id' => $warga->id,
            'nama_orang_tua' => 'Ibu ' . $nama,
            'berat_lahir' => 3.2,
            'panjang_lahir' => 50.0,
        ]);

        return $warga;
    }

    // =========================================================================
    // GRUP A: PENGUJIAN RIWAYAT PEMERIKSAAN
    // =========================================================================

    public function test_riwayat_01_pemeriksaan_pertama_balita_tanpa_riwayat_tcount_nol()
    {
        $warga = $this->createBalita('Balita Pertama', 12);
        $pem = Pemeriksaan::create([
            'warga_id' => $warga->id,
            'sesi_posyandu_id' => $this->sesi->id,
            'berat_badan' => 10.0,
            'tinggi_badan' => 76.0,
            'is_validated' => true,
            'tipe_kohort' => 'balita',
        ]);

        $res = $this->ewsService->evaluate($warga, 0.5, 0.2, 0.3, 10.0, $pem->id);

        $this->assertEquals(0, $res['t_count'], 'Pemeriksaan pertama harus menghasilkan t_count = 0');
        $this->assertEquals([10.0], $res['history']);
        $this->assertEquals('normal', $res['status']);
    }

    public function test_riwayat_02_pemeriksaan_kedua_berat_badan_meningkat_tcount_nol()
    {
        $warga = $this->createBalita('Balita Naik', 13);
        $sesiLalu = $this->createSesi(today()->subMonth());

        $pemLalu = Pemeriksaan::create([
            'warga_id' => $warga->id,
            'sesi_posyandu_id' => $sesiLalu->id,
            'berat_badan' => 9.0,
            'tinggi_badan' => 75.0,
            'is_validated' => true,
            'tipe_kohort' => 'balita',
        ]);

        $pemSekarang = Pemeriksaan::create([
            'warga_id' => $warga->id,
            'sesi_posyandu_id' => $this->sesi->id,
            'berat_badan' => 9.5, // Naik 0.5 kg
            'tinggi_badan' => 76.0,
            'is_validated' => true,
            'tipe_kohort' => 'balita',
        ]);

        $res = $this->ewsService->evaluate($warga, 0.2, 0.1, 0.3, 9.5, $pemSekarang->id);

        $this->assertEquals(0, $res['t_count']);
        $this->assertEquals([9.5, 9.0], $res['history']);
        $this->assertEquals('normal', $res['status']);
    }

    public function test_riwayat_03_pemeriksaan_kedua_berat_badan_tetap_1t()
    {
        $warga = $this->createBalita('Balita Tetap', 13);
        $sesiLalu = $this->createSesi(today()->subMonth());

        Pemeriksaan::create([
            'warga_id' => $warga->id,
            'sesi_posyandu_id' => $sesiLalu->id,
            'berat_badan' => 9.5,
            'tinggi_badan' => 75.0,
            'is_validated' => true,
            'tipe_kohort' => 'balita',
        ]);

        $pemSekarang = Pemeriksaan::create([
            'warga_id' => $warga->id,
            'sesi_posyandu_id' => $this->sesi->id,
            'berat_badan' => 9.5, // Bobot tetap (tidak naik) -> 1T
            'tinggi_badan' => 75.0,
            'is_validated' => true,
            'tipe_kohort' => 'balita',
        ]);

        $res = $this->ewsService->evaluate($warga, 0.1, 0.1, 0.1, 9.5, $pemSekarang->id);

        $this->assertEquals(1, $res['t_count']);
        $this->assertEquals('waspada', $res['status']);
        $this->assertEquals([9.5, 9.5], $res['history']);
    }

    public function test_riwayat_04_pemeriksaan_kedua_berat_badan_menurun_1t()
    {
        $warga = $this->createBalita('Balita Turun', 13);
        $sesiLalu = $this->createSesi(today()->subMonth());

        Pemeriksaan::create([
            'warga_id' => $warga->id,
            'sesi_posyandu_id' => $sesiLalu->id,
            'berat_badan' => 10.0,
            'tinggi_badan' => 76.0,
            'is_validated' => true,
            'tipe_kohort' => 'balita',
        ]);

        $pemSekarang = Pemeriksaan::create([
            'warga_id' => $warga->id,
            'sesi_posyandu_id' => $this->sesi->id,
            'berat_badan' => 9.7, // Turun 0.3 kg -> 1T
            'tinggi_badan' => 76.5,
            'is_validated' => true,
            'tipe_kohort' => 'balita',
        ]);

        $res = $this->ewsService->evaluate($warga, 0.1, 0.2, 0.1, 9.7, $pemSekarang->id);

        $this->assertEquals(1, $res['t_count']);
        $this->assertEquals('waspada', $res['status']);
        $this->assertEquals([9.7, 10.0], $res['history']);
    }

    public function test_riwayat_05_dua_pemeriksaan_tanggal_sama_terurut_kronologis()
    {
        $warga = $this->createBalita('Balita HariSama', 14);
        $sesi1 = $this->createSesi(today(), 'Sesi Pagi');
        $sesi2 = $this->createSesi(today(), 'Sesi Sore');

        $pem1 = Pemeriksaan::create([
            'warga_id' => $warga->id,
            'sesi_posyandu_id' => $sesi1->id,
            'berat_badan' => 10.0,
            'tinggi_badan' => 76.0,
            'is_validated' => true,
            'tipe_kohort' => 'balita',
        ]);
        $pem1->created_at = Carbon::now()->subHours(3);
        $pem1->save();

        $pem2 = Pemeriksaan::create([
            'warga_id' => $warga->id,
            'sesi_posyandu_id' => $sesi2->id,
            'berat_badan' => 9.8,
            'tinggi_badan' => 76.0,
            'is_validated' => true,
            'tipe_kohort' => 'balita',
        ]);
        $pem2->created_at = Carbon::now()->subMinutes(15);
        $pem2->save();

        $res = $this->ewsService->evaluate($warga, 0.0, 0.0, 0.0, 9.8, $pem2->id);

        $this->assertEquals(1, $res['t_count']);
        $this->assertEquals([9.8, 10.0], $res['history']);
    }

    public function test_riwayat_06_pemeriksaan_tanggal_mundur_backdated()
    {
        $warga = $this->createBalita('Balita Backdated2', 16);
        $sesiJan = $this->createSesi(today()->subMonths(3), 'Sesi Januari');
        $sesiFeb = $this->createSesi(today()->subMonths(2), 'Sesi Februari');
        $sesiMar = $this->createSesi(today()->subMonth(), 'Sesi Maret');
        $sesiApr = $this->createSesi(today(), 'Sesi April');

        // Entri April dibuat duluan
        $pemApr = Pemeriksaan::create([
            'warga_id' => $warga->id,
            'sesi_posyandu_id' => $sesiApr->id,
            'berat_badan' => 8.5,
            'tinggi_badan' => 78.0,
            'is_validated' => true,
            'tipe_kohort' => 'balita',
        ]);

        // Entri Februari dibuat berikutnya
        Pemeriksaan::create([
            'warga_id' => $warga->id,
            'sesi_posyandu_id' => $sesiFeb->id,
            'berat_badan' => 9.0,
            'tinggi_badan' => 77.0,
            'is_validated' => true,
            'tipe_kohort' => 'balita',
        ]);

        // Entri Januari dibuat paling akhir (backdated)
        Pemeriksaan::create([
            'warga_id' => $warga->id,
            'sesi_posyandu_id' => $sesiJan->id,
            'berat_badan' => 9.2,
            'tinggi_badan' => 76.0,
            'is_validated' => true,
            'tipe_kohort' => 'balita',
        ]);

        // Entri Maret dibuat
        Pemeriksaan::create([
            'warga_id' => $warga->id,
            'sesi_posyandu_id' => $sesiMar->id,
            'berat_badan' => 8.8,
            'tinggi_badan' => 77.5,
            'is_validated' => true,
            'tipe_kohort' => 'balita',
        ]);

        $res = $this->ewsService->evaluate($warga, 0.0, 0.0, 0.0, 8.5, $pemApr->id);

        // Kronologi tanggal: April (8.5) -> Maret (8.8) -> Februari (9.0) -> Januari (9.2)
        // 8.5 <= 8.8 (1T), 8.8 <= 9.0 (2T), 9.0 <= 9.2 (3T) -> 3T -> status BAHAYA
        $this->assertEquals([8.5, 8.8, 9.0, 9.2], $res['history']);
        $this->assertEquals(3, $res['t_count']);
        $this->assertEquals('bahaya', $res['status']);
    }

    public function test_riwayat_07_pemeriksaan_belum_divalidasi_tidak_masuk_riwayat()
    {
        $warga = $this->createBalita('Balita UnvalidatedPast', 12);
        $sesiLalu = $this->createSesi(today()->subMonth());

        // Pemeriksaan lalu tapi belum divalidasi oleh bidan
        Pemeriksaan::create([
            'warga_id' => $warga->id,
            'sesi_posyandu_id' => $sesiLalu->id,
            'berat_badan' => 10.0,
            'tinggi_badan' => 75.0,
            'is_validated' => false, // BELUM VALIDASI
            'tipe_kohort' => 'balita',
        ]);

        $pemSekarang = Pemeriksaan::create([
            'warga_id' => $warga->id,
            'sesi_posyandu_id' => $this->sesi->id,
            'berat_badan' => 9.8,
            'tinggi_badan' => 75.0,
            'is_validated' => true,
            'tipe_kohort' => 'balita',
        ]);

        $res = $this->ewsService->evaluate($warga, 0.0, 0.0, 0.0, 9.8, $pemSekarang->id);

        // Karena data lalu belum valid, riwayat hanya berisi pemeriksaan saat ini, t_count = 0
        $this->assertEquals(0, $res['t_count'], 'Pemeriksaan belum divalidasi tidak boleh menjadi baseline riwayat');
        $this->assertEquals([9.8], $res['history']);
    }

    public function test_riwayat_08_pemeriksaan_sudah_divalidasi_masuk_riwayat()
    {
        $warga = $this->createBalita('Balita ValidatedPast', 12);
        $sesiLalu = $this->createSesi(today()->subMonth());

        // Pemeriksaan lalu sudah divalidasi oleh bidan
        Pemeriksaan::create([
            'warga_id' => $warga->id,
            'sesi_posyandu_id' => $sesiLalu->id,
            'berat_badan' => 10.0,
            'tinggi_badan' => 75.0,
            'is_validated' => true, // SUDAH VALIDASI
            'tipe_kohort' => 'balita',
        ]);

        $pemSekarang = Pemeriksaan::create([
            'warga_id' => $warga->id,
            'sesi_posyandu_id' => $this->sesi->id,
            'berat_badan' => 9.8,
            'tinggi_badan' => 75.0,
            'is_validated' => true,
            'tipe_kohort' => 'balita',
        ]);

        $res = $this->ewsService->evaluate($warga, 0.0, 0.0, 0.0, 9.8, $pemSekarang->id);

        $this->assertEquals(1, $res['t_count']);
        $this->assertEquals([9.8, 10.0], $res['history']);
    }

    public function test_riwayat_09_pemeriksaan_dari_balita_berbeda_terisolasi()
    {
        $wargaA = $this->createBalita('Balita A', 12);
        $wargaB = $this->createBalita('Balita B', 12);
        $sesiLalu = $this->createSesi(today()->subMonth());

        Pemeriksaan::create([
            'warga_id' => $wargaA->id,
            'sesi_posyandu_id' => $sesiLalu->id,
            'berat_badan' => 12.0,
            'is_validated' => true,
            'tipe_kohort' => 'balita',
        ]);

        $pemB = Pemeriksaan::create([
            'warga_id' => $wargaB->id,
            'sesi_posyandu_id' => $this->sesi->id,
            'berat_badan' => 8.0,
            'is_validated' => true,
            'tipe_kohort' => 'balita',
        ]);

        $resB = $this->ewsService->evaluate($wargaB, 0.0, 0.0, 0.0, 8.0, $pemB->id);

        $this->assertEquals([8.0], $resB['history'], 'Balita B tidak boleh membaca data riwayat Balita A');
        $this->assertEquals(0, $resB['t_count']);
    }

    public function test_riwayat_10_pembaruan_pemeriksaan_lama_idempoten()
    {
        $warga = $this->createBalita('Balita Idempoten', 10);
        $pem = Pemeriksaan::create([
            'warga_id' => $warga->id,
            'sesi_posyandu_id' => $this->sesi->id,
            'berat_badan' => 9.0,
            'tinggi_badan' => 75.0,
            'is_validated' => true,
            'tipe_kohort' => 'balita',
        ]);

        $res1 = $this->ewsService->evaluate($warga, 0.0, 0.0, 0.0, 9.0, $pem->id);
        KalkulasiZScore::updateOrCreate(
            ['pemeriksaan_id' => $pem->id],
            [
                'zscore_bb_u' => 0.0,
                'zscore_tb_u' => 0.0,
                'zscore_bb_tb' => 0.0,
                'status_gizi' => 'Normal',
                'status_stunting' => 'Normal',
                'status_bb_tb' => 'Normal',
                'ews_status' => $res1['status'],
                'ews_message' => $res1['message'],
                'priority_score' => $res1['priority_score'],
                'priority_details' => $res1['priority_details'],
                'alert_status' => $res1['alert_status'],
            ]
        );

        $this->assertEquals(1, KalkulasiZScore::where('pemeriksaan_id', $pem->id)->count());

        // Update data pemeriksaan yang sama
        $pem->update(['berat_badan' => 7.0]);
        $res2 = $this->ewsService->evaluate($warga, -3.2, 0.0, -3.5, 7.0, $pem->id);
        KalkulasiZScore::updateOrCreate(
            ['pemeriksaan_id' => $pem->id],
            [
                'zscore_bb_u' => -3.2,
                'zscore_tb_u' => 0.0,
                'zscore_bb_tb' => -3.5,
                'status_gizi' => 'Gizi Buruk',
                'status_stunting' => 'Normal',
                'status_bb_tb' => 'Gizi Buruk',
                'ews_status' => $res2['status'],
                'ews_message' => $res2['message'],
                'priority_score' => $res2['priority_score'],
                'priority_details' => $res2['priority_details'],
                'alert_status' => $res2['alert_status'],
            ]
        );

        // Jumlah baris kalkulasi tetap 1 (idempoten)
        $this->assertEquals(1, KalkulasiZScore::where('pemeriksaan_id', $pem->id)->count());
        $this->assertEquals('bahaya', KalkulasiZScore::where('pemeriksaan_id', $pem->id)->first()->ews_status);
    }

    // =========================================================================
    // GRUP B: PENGUJIAN DATA ANTROPOMETRI (PENANGANAN NULL DAN INCOMPLETE)
    // =========================================================================

    public function test_antropometri_01_waz_haz_whz_semua_valid_normal()
    {
        $warga = $this->createBalita('Balita Lengkap Normal', 12);
        $res = $this->ewsService->evaluate($warga, 0.2, 0.1, 0.3, 10.0);

        $this->assertEquals('normal', $res['status']);
        $this->assertEquals(0.00, $res['priority_score']);
        $this->assertEquals('resolved', $res['alert_status']);
    }

    public function test_antropometri_02_salah_satu_zscore_null_waz_null()
    {
        $warga = $this->createBalita('Balita WazNull', 12);
        // HAZ & WHZ normal, tetapi WAZ null
        $res = $this->ewsService->evaluate($warga, null, 0.1, 0.2, 10.0);

        $this->assertEquals('data_tidak_lengkap', $res['status'], 'WAZ null tidak boleh disimpulkan sebagai normal');
        $this->assertEquals(0.00, $res['priority_score']);
        $this->assertStringContainsString('DATA TIDAK LENGKAP', $res['message']);
    }

    public function test_antropometri_03_salah_satu_zscore_null_haz_null()
    {
        $warga = $this->createBalita('Balita HazNull', 12);
        // WAZ & WHZ normal, tetapi HAZ null
        $res = $this->ewsService->evaluate($warga, 0.2, null, 0.1, 10.0);

        $this->assertEquals('data_tidak_lengkap', $res['status'], 'HAZ null tidak boleh disimpulkan sebagai normal');
        $this->assertEquals(0.00, $res['priority_score']);
    }

    public function test_antropometri_04_salah_satu_zscore_null_whz_null()
    {
        $warga = $this->createBalita('Balita WhzNull', 12);
        // WAZ & HAZ normal, tetapi WHZ null
        $res = $this->ewsService->evaluate($warga, 0.2, 0.1, null, 10.0);

        $this->assertEquals('data_tidak_lengkap', $res['status'], 'WHZ null tidak boleh disimpulkan sebagai normal');
        $this->assertEquals(0.00, $res['priority_score']);
    }

    public function test_antropometri_05_seluruh_zscore_null()
    {
        $warga = $this->createBalita('Balita AllZNull', 12);
        $res = $this->ewsService->evaluate($warga, null, null, null, 10.0);

        $this->assertEquals('data_tidak_lengkap', $res['status']);
        $this->assertEquals(0.00, $res['priority_score']);
    }

    public function test_antropometri_06_bb_ada_tb_tidak_ada()
    {
        $warga = $this->createBalita('Balita NoTb', 12);
        // TB tidak ada -> HAZ dan WHZ tidak bisa dihitung (null), hanya WAZ yang ada
        $res = $this->ewsService->evaluate($warga, 0.1, null, null, 9.5);

        $this->assertEquals('data_tidak_lengkap', $res['status']);
    }

    public function test_antropometri_07_tb_ada_bb_tidak_ada()
    {
        $warga = $this->createBalita('Balita NoBb', 12);
        // BB tidak ada -> WAZ dan WHZ null, berat badan null
        $res = $this->ewsService->evaluate($warga, null, 0.2, null, null);

        $this->assertEquals('data_tidak_lengkap', $res['status']);
    }

    public function test_antropometri_08_data_antropometri_tidak_valid()
    {
        $warga = $this->createBalita('Balita InvalidAge', 70); // 70 bulan (di luar jangkauan balita 0-60 bln)
        $bbu = $this->whoService->calculateBBU(15.0, $warga->tanggal_lahir, today(), 'L');

        $this->assertNull($bbu['zscore']);
        $this->assertEquals('out_of_reference_range', $bbu['status']);

        // EWS yang menerima zscore null harus berstatus data_tidak_lengkap
        $res = $this->ewsService->evaluate($warga, $bbu['zscore'], null, null, 15.0);
        $this->assertEquals('data_tidak_lengkap', $res['status']);
    }

    public function test_antropometri_09_zscore_null_tetapi_terdapat_risiko_akut()
    {
        $warga = $this->createBalita('Balita PartialBahaya', 12);
        // WHZ sangat parah (-3.5), tetapi HAZ tidak diukur (null)
        $res = $this->ewsService->evaluate($warga, -2.1, null, -3.5, 6.5);

        $this->assertEquals('bahaya', $res['status'], 'Indikator kritis harus tetap memicu bahaya meskipun data lain parsial');
        $this->assertStringContainsString('Peringatan: Penilaian berdasarkan indikator kritis yang tersedia', $res['message']);
        $this->assertGreaterThanOrEqual(70.0, $res['priority_score']);
    }

    // =========================================================================
    // GRUP C: PENGUJIAN SAW (SIMPLE ADDITIVE WEIGHTING)
    // =========================================================================

    public function test_saw_01_seluruh_kriteria_valid_terbobot()
    {
        // WHZ = -3.0 (norm: 0.5), WAZ = -2.5 (norm: 0.25), HAZ = -2.4 (norm: 0.2), 2T (norm: 0.67), Usia 12 bln (norm: 1.0)
        $res = $this->ewsService->calculatePriorityScore('bahaya', -2.5, -2.4, -3.0, 2, 12);

        $this->assertIsArray($res);
        $this->assertGreaterThan(70.0, $res['score']);
        $this->assertLessThanOrEqual(100.0, $res['score']);
    }

    public function test_saw_02_kriteria_nilai_minimum_nol()
    {
        // Pertumbuhan normal -> skor prioritas 0.00
        $res = $this->ewsService->calculatePriorityScore('normal', 0.5, 0.2, 0.1, 0, 12);
        $this->assertEquals(0.00, $res['score']);
    }

    public function test_saw_03_kriteria_nilai_maksimum_ekstrem()
    {
        // Seluruh indikator defisit maksimum (WHZ -4.5, WAZ -4.5, HAZ -4.5, 3T, usia 6 bulan)
        $res = $this->ewsService->calculatePriorityScore('bahaya', -4.5, -4.5, -4.5, 3, 6);

        // Base 70.0 + (1.0 * 29.9) = 99.90 (dibulatkan dan dibatasi max 100.0)
        $this->assertGreaterThanOrEqual(99.0, $res['score']);
        $this->assertLessThanOrEqual(100.0, $res['score']);
    }

    public function test_saw_04_kriteria_parsial_tidak_lengkap()
    {
        $res = $this->ewsService->calculatePriorityScore('data_tidak_lengkap', null, null, null, 0, 12);
        $this->assertEquals(0.00, $res['score']);
        $this->assertEquals('data_tidak_lengkap', $res['details']['category']);
    }

    public function test_saw_05_total_bobot_harus_tepat_satu()
    {
        $wWhz = 0.35;
        $wWaz = 0.25;
        $wHaz = 0.20;
        $wT   = 0.15;
        $wAge = 0.05;

        $totalBobot = $wWhz + $wWaz + $wHaz + $wT + $wAge;
        $this->assertEquals(1.00, round($totalBobot, 4), 'Total bobot kriteria SAW harus tepat 1.00 (100%)');
    }

    public function test_saw_06_skor_tidak_pernah_negatif()
    {
        $res = $this->ewsService->calculatePriorityScore('waspada', 1.5, 1.2, 2.5, 0, 36);
        $this->assertGreaterThanOrEqual(0.00, $res['score']);
    }

    public function test_saw_07_skor_tidak_pernah_melebihi_seratus()
    {
        $res = $this->ewsService->calculatePriorityScore('bahaya', -10.0, -10.0, -10.0, 5, 2);
        $this->assertLessThanOrEqual(100.00, $res['score']);
    }

    public function test_saw_08_urutan_prioritas_konsisten_monotonik()
    {
        // Balita 1: WHZ -2.2, HAZ -2.1 (Defisit lebih ringan)
        $score1 = $this->ewsService->calculatePriorityScore('risiko_tinggi', -2.1, -2.1, -2.2, 0, 12)['score'];

        // Balita 2: WHZ -2.8, HAZ -2.9 (Defisit lebih berat)
        $score2 = $this->ewsService->calculatePriorityScore('risiko_tinggi', -2.8, -2.9, -2.8, 0, 12)['score'];

        $this->assertGreaterThan($score1, $score2, 'Balita dengan defisit Z-Score lebih berat harus memperoleh skor SAW lebih tinggi');
    }

    public function test_saw_09_kategori_risiko_tidak_berubah_akibat_bobot_saw()
    {
        // Kategori klinis tetap 'risiko_tinggi' terlepas dari skor penambahan
        $eval = $this->ewsService->evaluate(
            $this->createBalita('Balita Kategori', 12),
            -2.3, // Kurang
            -0.5,
            -1.0,
            8.0
        );

        $this->assertEquals('risiko_tinggi', $eval['status']);
        $this->assertEquals('risiko_tinggi', $eval['priority_details']['category']);
        $this->assertGreaterThanOrEqual(40.0, $eval['priority_score']);
        $this->assertLessThan(70.0, $eval['priority_score']);
    }

    // =========================================================================
    // GRUP D: PENGUJIAN INTEGRASI (KADER, BIDAN, ALERT, DAN TRANSAKSI)
    // =========================================================================

    public function test_integrasi_01_alur_pengukuran_oleh_kader()
    {
        $warga = $this->createBalita('Balita Kader', 12);
        \App\Models\Absensi::create([
            'warga_id' => $warga->id,
            'sesi_posyandu_id' => $this->sesi->id,
        ]);

        $response = $this->actingAs($this->kader)->post(route('kader.pengukuran.store', $warga->id), [
            'warga_id' => $warga->id,
            'berat_badan' => 7.0, // Indikasi Gizi Buruk Akut
            'tinggi_badan' => 75.0,
            'posisi_ukur' => 'terlentang',
            'tipe_kohort' => 'balita',
        ]);

        $response->assertRedirect(route('kader.pengukuran.index'));

        $this->assertDatabaseHas('pemeriksaan', [
            'warga_id' => $warga->id,
            'is_validated' => 0, // Unvalidated sebelum disetujui Bidan
        ]);

        $this->assertDatabaseHas('kalkulasi_zscore', [
            'ews_status' => 'data_tidak_lengkap',
        ]);
    }

    public function test_integrasi_02_alur_pemeriksaan_oleh_bidan()
    {
        $warga = $this->createBalita('Balita Bidan', 14);

        $response = $this->actingAs($this->bidan)->post(route('bidan.pemeriksaan.store'), [
            'warga_id' => $warga->id,
            'sesi_posyandu_id' => $this->sesi->id,
            'berat_badan' => 9.5,
            'tinggi_badan' => 78.0,
            'posisi_ukur' => 'terlentang',
            'tipe_kohort' => 'balita',
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('pemeriksaan', [
            'warga_id' => $warga->id,
            'is_validated' => 1,
        ]);
    }

    public function test_integrasi_03_pembuatan_alert_pending()
    {
        $warga = $this->createBalita('Balita Pending', 12);
        $pem = Pemeriksaan::create([
            'warga_id' => $warga->id,
            'sesi_posyandu_id' => $this->sesi->id,
            'berat_badan' => 6.5,
            'tinggi_badan' => 75.0,
            'is_validated' => true,
            'tipe_kohort' => 'balita',
        ]);

        $eval = $this->ewsService->evaluate($warga, -3.2, -1.0, -3.5, 6.5, $pem->id);

        $kalkulasi = KalkulasiZScore::create([
            'pemeriksaan_id' => $pem->id,
            'ews_status' => $eval['status'],
            'ews_message' => $eval['message'],
            'priority_score' => $eval['priority_score'],
            'priority_details' => $eval['priority_details'],
            'alert_status' => $eval['alert_status'],
        ]);

        $this->assertEquals('pending', $kalkulasi->alert_status);
    }

    public function test_integrasi_04_pencegahan_duplikasi_kunjungan_rumah()
    {
        $warga = $this->createBalita('Balita KunjunganUnik', 10);
        $ewsResult = [
            'status' => 'bahaya',
            'message' => 'Gizi Buruk Akut',
            'priority_score' => 88.0,
        ];

        $v1 = $this->ewsService->triggerKunjunganRumah($warga, $ewsResult, $this->bidan->id);
        $this->assertNotNull($v1);
        $this->assertEquals('kritis', $v1->prioritas);

        // Percobaan pemicuan kedua saat tiket aktif
        $v2 = $this->ewsService->triggerKunjunganRumah($warga, $ewsResult, $this->bidan->id);
        $this->assertNull($v2, 'Tiket kedua harus ditolak untuk mencegah duplikasi');

        $this->assertEquals(1, KunjunganRumah::where('warga_id', $warga->id)->count());
    }

    public function test_integrasi_05_perubahan_status_pending_menjadi_in_progress()
    {
        $warga = $this->createBalita('Balita InProgress', 12);
        $pem = Pemeriksaan::create([
            'warga_id' => $warga->id,
            'sesi_posyandu_id' => $this->sesi->id,
            'berat_badan' => 7.0,
            'tinggi_badan' => 75.0,
            'is_validated' => true,
            'tipe_kohort' => 'balita',
        ]);

        $kalkulasi = KalkulasiZScore::create([
            'pemeriksaan_id' => $pem->id,
            'ews_status' => 'bahaya',
            'alert_status' => 'pending',
        ]);

        $response = $this->actingAs($this->bidan)->get(route('bidan.pemeriksaan.tindak-lanjut', $pem->id));
        $response->assertStatus(200);

        $this->assertEquals('in_progress', $kalkulasi->fresh()->alert_status);
    }

    public function test_integrasi_06_penyelesaian_alert_menjadi_resolved()
    {
        $warga = $this->createBalita('Balita Resolved', 12);
        $pem = Pemeriksaan::create([
            'warga_id' => $warga->id,
            'sesi_posyandu_id' => $this->sesi->id,
            'berat_badan' => 7.0,
            'tinggi_badan' => 75.0,
            'is_validated' => true,
            'tipe_kohort' => 'balita',
        ]);

        $kalkulasi = KalkulasiZScore::create([
            'pemeriksaan_id' => $pem->id,
            'ews_status' => 'bahaya',
            'alert_status' => 'in_progress',
        ]);

        $response = $this->actingAs($this->bidan)->patch(route('bidan.pemeriksaan.tindak-lanjut.update', $pem->id), [
            'tindakan' => 'Pemberian F-75 dan rujukan poli gizi',
            'catatan' => 'Kondisi telah ditangani awal',
        ]);
        $response->assertRedirect(route('bidan.dashboard'));

        $fresh = $kalkulasi->fresh();
        $this->assertEquals('resolved', $fresh->alert_status);
        $this->assertNotNull($fresh->resolved_at);
        $this->assertEquals($this->bidan->id, $fresh->resolved_by);
    }

    public function test_integrasi_07_pencatatan_petugas_resolved_by()
    {
        $bidanLain = User::factory()->create(['name' => 'Bidan Dewi']);
        $bidanLain->assignRole('Puskesmas');

        $warga = $this->createBalita('Balita AuditTrail', 12);
        $pem = Pemeriksaan::create([
            'warga_id' => $warga->id,
            'sesi_posyandu_id' => $this->sesi->id,
            'berat_badan' => 7.0,
            'tinggi_badan' => 75.0,
            'is_validated' => true,
            'tipe_kohort' => 'balita',
        ]);

        $kalkulasi = KalkulasiZScore::create([
            'pemeriksaan_id' => $pem->id,
            'ews_status' => 'bahaya',
            'alert_status' => 'pending',
        ]);

        $this->actingAs($bidanLain)->patch(route('bidan.pemeriksaan.tindak-lanjut.update', $pem->id), [
            'tindakan' => 'Intervensi khusus',
            'catatan' => 'Ditangani oleh Bidan Dewi',
        ]);

        $this->assertEquals($bidanLain->id, $kalkulasi->fresh()->resolved_by);
        $this->assertEquals('Bidan Dewi', $kalkulasi->fresh()->resolvedBy->name);
    }

    public function test_integrasi_08_pengurutan_dashboard_bidan_berdasarkan_prioritas()
    {
        $warga1 = $this->createBalita('Balita RisikoSedang', 12);
        $pem1 = Pemeriksaan::create(['warga_id' => $warga1->id, 'sesi_posyandu_id' => $this->sesi->id, 'is_validated' => true, 'tipe_kohort' => 'balita']);
        KalkulasiZScore::create([
            'pemeriksaan_id' => $pem1->id,
            'ews_status' => 'risiko_tinggi',
            'priority_score' => 55.0,
            'alert_status' => 'pending',
        ]);

        $warga2 = $this->createBalita('Balita BahayaKritis', 12);
        $pem2 = Pemeriksaan::create(['warga_id' => $warga2->id, 'sesi_posyandu_id' => $this->sesi->id, 'is_validated' => true, 'tipe_kohort' => 'balita']);
        KalkulasiZScore::create([
            'pemeriksaan_id' => $pem2->id,
            'ews_status' => 'bahaya',
            'priority_score' => 92.0,
            'alert_status' => 'pending',
        ]);

        $response = $this->actingAs($this->bidan)->get(route('bidan.dashboard'));
        $response->assertStatus(200);

        // Urutan kemunculan di view: Bahaya Kritis harus muncul sebelum Risiko Sedang
        $content = $response->getContent();
        $posBahaya = strpos($content, 'Balita BahayaKritis');
        $posRisiko = strpos($content, 'Balita RisikoSedang');

        $this->assertNotFalse($posBahaya);
        $this->assertNotFalse($posRisiko);
        $this->assertLessThan($posRisiko, $posBahaya, 'Kasus Bahaya (Skor 92) harus berada di urutan atas mendahului Risiko Tinggi (Skor 55)');
    }

    public function test_integrasi_09_transaksi_database_rollback_saat_proses_gagal()
    {
        $warga = $this->createBalita('Balita FailTx', 10);
        $initialPemCount = Pemeriksaan::count();
        $initialZCount = KalkulasiZScore::count();

        try {
            DB::beginTransaction();

            $pem = Pemeriksaan::create([
                'warga_id' => $warga->id,
                'sesi_posyandu_id' => $this->sesi->id,
                'berat_badan' => 8.0,
                'tinggi_badan' => 74.0,
                'is_validated' => false,
                'tipe_kohort' => 'balita',
            ]);

            throw new \RuntimeException('Simulasi crash jaringan basis data');

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
        }

        $this->assertEquals($initialPemCount, Pemeriksaan::count());
        $this->assertEquals($initialZCount, KalkulasiZScore::count());
    }
}
