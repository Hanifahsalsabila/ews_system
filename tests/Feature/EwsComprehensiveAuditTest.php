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
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EwsComprehensiveAuditTest extends TestCase
{
    use RefreshDatabase;

    protected $bidan;
    protected $kader;
    protected $region;
    protected $sesi;
    protected $ewsService;

    protected function setUp(): void
    {
        parent::setUp();

        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Puskesmas']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Kader_Posyandu']);

        $this->bidan = User::factory()->create();
        $this->bidan->assignRole('Puskesmas');

        $this->kader = User::factory()->create();
        $this->kader->assignRole('Kader_Posyandu');

        $this->region = Region::create(['name' => 'Dusun Sejahtera', 'type' => 'dusun']);
        $this->sesi = $this->createSesi(today(), 'Sesi Penimbangan Rutin');

        $this->ewsService = app(EwsService::class);
    }

    private function createSesi($date = null, string $nama = 'Sesi'): SesiPosyandu
    {
        return SesiPosyandu::create([
            'status' => 'aktif',
            'tanggal' => $date ?? today(),
            'nama_sesi' => $nama . ' ' . uniqid(),
            'lokasi' => 'Balai Warga',
            'waktu_mulai' => '08:00',
            'waktu_selesai' => '12:00',
        ]);
    }

    private function createBalita(string $nama, int $ageMonths = 12): Warga
    {
        $warga = Warga::create([
            'nama' => $nama,
            'nik' => '3201' . rand(100000000000, 999999999999),
            'tanggal_lahir' => today()->subMonths($ageMonths),
            'jenis_kelamin' => 'L',
            'alamat' => 'RT 01 RW 02',
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

    /**
     * Skenario 1: Pemeriksaan pertama dengan semua Z-Score normal
     * Validasi: Tidak menghasilkan false positive waspada (Self-comparison fix)
     */
    public function test_skenario_01_pemeriksaan_pertama_normal_tidak_waspada()
    {
        $warga = $this->createBalita('Balita Sehat 1', 12);
        
        $pem = Pemeriksaan::create([
            'warga_id' => $warga->id,
            'sesi_posyandu_id' => $this->sesi->id,
            'berat_badan' => 10.0,
            'tinggi_badan' => 76.0,
            'is_validated' => true,
            'tipe_kohort' => 'balita',
        ]);

        $res = $this->ewsService->evaluate(
            $warga,
            0.5,  // WAZ normal
            0.2,  // HAZ normal
            0.4,  // WHZ normal
            10.0, // BB saat ini
            $pem->id
        );

        $this->assertEquals('normal', $res['status']);
        $this->assertEquals(0, $res['t_count']);
        $this->assertEquals(0.00, $res['priority_score']);
        $this->assertEquals('resolved', $res['alert_status']);
    }

    /**
     * Skenario 2: Pemeriksaan pertama dengan WAZ di bawah -2 SD
     */
    public function test_skenario_02_pemeriksaan_pertama_waz_di_bawah_minus_2_sd()
    {
        $warga = $this->createBalita('Balita Underweight', 14);

        $pem = Pemeriksaan::create([
            'warga_id' => $warga->id,
            'sesi_posyandu_id' => $this->sesi->id,
            'berat_badan' => 7.8,
            'tinggi_badan' => 75.0,
            'is_validated' => true,
            'tipe_kohort' => 'balita',
        ]);

        $res = $this->ewsService->evaluate($warga, -2.4, -0.5, -0.8, 7.8, $pem->id);

        $this->assertEquals('risiko_tinggi', $res['status']);
        $this->assertStringContainsString('BB/U < -2 SD', $res['message']);
        $this->assertEquals('pending', $res['alert_status']);
        $this->assertGreaterThanOrEqual(40.0, $res['priority_score']);
    }

    /**
     * Skenario 3: Pemeriksaan dengan HAZ di bawah -3 SD (Severe Stunting)
     */
    public function test_skenario_03_pemeriksaan_haz_di_bawah_minus_3_sd()
    {
        $warga = $this->createBalita('Balita Stunted Berat', 20);

        $pem = Pemeriksaan::create([
            'warga_id' => $warga->id,
            'sesi_posyandu_id' => $this->sesi->id,
            'berat_badan' => 9.5,
            'tinggi_badan' => 70.0,
            'is_validated' => true,
            'tipe_kohort' => 'balita',
        ]);

        $res = $this->ewsService->evaluate($warga, -1.2, -3.4, -0.5, 9.5, $pem->id);

        $this->assertEquals('bahaya', $res['status']);
        $this->assertStringContainsString('TB/U < -3 SD', $res['message']);
        $this->assertGreaterThanOrEqual(70.0, $res['priority_score']);
    }

    /**
     * Skenario 4: Pemeriksaan dengan WHZ di bawah -3 SD (Gizi Buruk Akut)
     * Validasi: WHZ diperhitungkan dan memicu status bahaya
     */
    public function test_skenario_04_pemeriksaan_whz_di_bawah_minus_3_sd()
    {
        $warga = $this->createBalita('Balita Wasting Akut', 10);

        $pem = Pemeriksaan::create([
            'warga_id' => $warga->id,
            'sesi_posyandu_id' => $this->sesi->id,
            'berat_badan' => 6.2,
            'tinggi_badan' => 72.0,
            'is_validated' => true,
            'tipe_kohort' => 'balita',
        ]);

        // WAZ & HAZ normal-rendah, tapi WHZ kritis (-3.5 SD)
        $res = $this->ewsService->evaluate($warga, -1.8, -1.0, -3.5, 6.2, $pem->id);

        $this->assertEquals('bahaya', $res['status']);
        $this->assertStringContainsString('BB/TB < -3 SD', $res['message']);
        $this->assertGreaterThanOrEqual(70.0, $res['priority_score']);
    }

    /**
     * Skenario 5: Pemeriksaan dengan beberapa indikator bermasalah
     */
    public function test_skenario_05_beberapa_indikator_bermasalah()
    {
        $warga = $this->createBalita('Balita Multi Masalah', 18);

        $pem = Pemeriksaan::create([
            'warga_id' => $warga->id,
            'sesi_posyandu_id' => $this->sesi->id,
            'berat_badan' => 8.0,
            'tinggi_badan' => 73.0,
            'is_validated' => true,
            'tipe_kohort' => 'balita',
        ]);

        // Defisit ganda: WAZ (-2.5), HAZ (-2.4), WHZ (-2.3)
        $res = $this->ewsService->evaluate($warga, -2.5, -2.4, -2.3, 8.0, $pem->id);

        $this->assertEquals('risiko_tinggi', $res['status']);
        $this->assertGreaterThan(45.0, $res['priority_score']);
        $this->assertCount(4, $res['priority_details']['factors']); // 3 defisit + usia <= 24
    }

    /**
     * Skenario 6: Berat badan meningkat dibanding pemeriksaan sebelumnya
     * Validasi: Kenaikan bobot tidak menghasilkan t_count dan tetap normal
     */
    public function test_skenario_06_berat_badan_meningkat_dibanding_pemeriksaan_sebelumnya()
    {
        $warga = $this->createBalita('Balita Naik BB', 15);
        $sesiLalu = $this->createSesi(today()->subMonth());

        // Pemeriksaan bulan lalu: 8.5 kg
        $pemLalu = Pemeriksaan::create([
            'warga_id' => $warga->id,
            'sesi_posyandu_id' => $sesiLalu->id,
            'berat_badan' => 8.5,
            'tinggi_badan' => 77.0,
            'is_validated' => true,
            'tipe_kohort' => 'balita',
        ]);
        $pemLalu->created_at = now()->subMonth();
        $pemLalu->save();

        // Pemeriksaan sekarang: 9.1 kg (NAIK 0.6 kg)
        $pemSekarang = Pemeriksaan::create([
            'warga_id' => $warga->id,
            'sesi_posyandu_id' => $this->sesi->id,
            'berat_badan' => 9.1,
            'tinggi_badan' => 78.0,
            'is_validated' => true,
            'tipe_kohort' => 'balita',
        ]);

        $res = $this->ewsService->evaluate($warga, 0.2, 0.1, 0.3, 9.1, $pemSekarang->id);

        $this->assertEquals('normal', $res['status']);
        $this->assertEquals(0, $res['t_count']);
    }

    /**
     * Skenario 7: Berat badan stagnan pada 1x pemeriksaan (1T)
     */
    public function test_skenario_07_berat_badan_stagnan_satu_kali_pemeriksaan()
    {
        $warga = $this->createBalita('Balita Stagnan 1T', 12);
        $sesiLalu = $this->createSesi(today()->subMonth());

        // Bulan lalu: 9.0 kg
        $pemLalu = Pemeriksaan::create([
            'warga_id' => $warga->id,
            'sesi_posyandu_id' => $sesiLalu->id,
            'berat_badan' => 9.0,
            'tinggi_badan' => 75.0,
            'is_validated' => true,
            'tipe_kohort' => 'balita',
        ]);
        $pemLalu->created_at = now()->subMonth();
        $pemLalu->save();

        // Sekarang: tetap 9.0 kg (Stagnan 1T)
        $pemSekarang = Pemeriksaan::create([
            'warga_id' => $warga->id,
            'sesi_posyandu_id' => $this->sesi->id,
            'berat_badan' => 9.0,
            'tinggi_badan' => 75.5,
            'is_validated' => true,
            'tipe_kohort' => 'balita',
        ]);

        $res = $this->ewsService->evaluate($warga, 0.1, 0.2, 0.1, 9.0, $pemSekarang->id);

        $this->assertEquals('waspada', $res['status']);
        $this->assertEquals(1, $res['t_count']);
        $this->assertStringContainsString('1T', $res['message']);
    }

    /**
     * Skenario 8: Berat badan tidak naik dalam 2x berturut-turut (2T)
     */
    public function test_skenario_08_berat_badan_tidak_naik_dua_kali_berturut_turut()
    {
        $warga = $this->createBalita('Balita 2T', 16);
        $sesi2BulanLalu = $this->createSesi(today()->subMonths(2));
        $sesi1BulanLalu = $this->createSesi(today()->subMonth());

        // 2 bulan lalu: 9.5 kg
        $pem2 = Pemeriksaan::create([
            'warga_id' => $warga->id,
            'sesi_posyandu_id' => $sesi2BulanLalu->id,
            'berat_badan' => 9.5,
            'tinggi_badan' => 78.0,
            'is_validated' => true,
            'tipe_kohort' => 'balita',
        ]);
        $pem2->created_at = now()->subMonths(2);
        $pem2->save();

        // 1 bulan lalu: 9.3 kg (turun)
        $pem1 = Pemeriksaan::create([
            'warga_id' => $warga->id,
            'sesi_posyandu_id' => $sesi1BulanLalu->id,
            'berat_badan' => 9.3,
            'tinggi_badan' => 78.5,
            'is_validated' => true,
            'tipe_kohort' => 'balita',
        ]);
        $pem1->created_at = now()->subMonth();
        $pem1->save();

        // Sekarang: 9.2 kg (turun lagi -> 2T)
        $pemSekarang = Pemeriksaan::create([
            'warga_id' => $warga->id,
            'sesi_posyandu_id' => $this->sesi->id,
            'berat_badan' => 9.2,
            'tinggi_badan' => 79.0,
            'is_validated' => true,
            'tipe_kohort' => 'balita',
        ]);

        $res = $this->ewsService->evaluate($warga, 0.1, 0.1, 0.1, 9.2, $pemSekarang->id);

        $this->assertEquals(2, $res['t_count']);
        $this->assertEquals('risiko_tinggi', $res['status']);
        $this->assertStringContainsString('2T', $res['message']);
    }

    /**
     * Skenario 9: Berat badan tidak naik dalam 3x berturut-turut (3T)
     */
    public function test_skenario_09_berat_badan_tidak_naik_tiga_kali_berturut_turut()
    {
        $warga = $this->createBalita('Balita 3T', 18);
        $sesi3 = $this->createSesi(today()->subMonths(3));
        $sesi2 = $this->createSesi(today()->subMonths(2));
        $sesi1 = $this->createSesi(today()->subMonth());

        $p3 = Pemeriksaan::create(['warga_id' => $warga->id, 'sesi_posyandu_id' => $sesi3->id, 'berat_badan' => 10.0, 'tinggi_badan' => 80.0, 'is_validated' => true, 'tipe_kohort' => 'balita']);
        $p3->created_at = now()->subMonths(3);
        $p3->save();

        $p2 = Pemeriksaan::create(['warga_id' => $warga->id, 'sesi_posyandu_id' => $sesi2->id, 'berat_badan' => 9.9, 'tinggi_badan' => 80.5, 'is_validated' => true, 'tipe_kohort' => 'balita']);
        $p2->created_at = now()->subMonths(2);
        $p2->save();

        $p1 = Pemeriksaan::create(['warga_id' => $warga->id, 'sesi_posyandu_id' => $sesi1->id, 'berat_badan' => 9.8, 'tinggi_badan' => 81.0, 'is_validated' => true, 'tipe_kohort' => 'balita']);
        $p1->created_at = now()->subMonth();
        $p1->save();

        // Sekarang: 9.7 kg -> 3T!
        $pemSekarang = Pemeriksaan::create(['warga_id' => $warga->id, 'sesi_posyandu_id' => $this->sesi->id, 'berat_badan' => 9.7, 'tinggi_badan' => 81.5, 'is_validated' => true, 'tipe_kohort' => 'balita']);

        $res = $this->ewsService->evaluate($warga, 0.1, 0.1, 0.1, 9.7, $pemSekarang->id);

        $this->assertEquals(3, $res['t_count']);
        $this->assertEquals('bahaya', $res['status']);
        $this->assertStringContainsString('3T', $res['message']);
    }

    /**
     * Skenario 10: Pemeriksaan dengan nilai Z-Score null
     */
    public function test_skenario_10_pemeriksaan_dengan_nilai_zscore_null()
    {
        $warga = $this->createBalita('Balita Null Z', 12);

        $pem = Pemeriksaan::create([
            'warga_id' => $warga->id,
            'sesi_posyandu_id' => $this->sesi->id,
            'berat_badan' => 10.0,
            'tinggi_badan' => 75.0,
            'is_validated' => true,
            'tipe_kohort' => 'balita',
        ]);

        $res = $this->ewsService->evaluate($warga, null, null, null, 10.0, $pem->id);

        $this->assertEquals('normal', $res['status']);
        $this->assertEquals(0.0, $res['priority_score']);
    }

    /**
     * Skenario 11: Pemeriksaan kader yang belum divalidasi tidak tampil di dashboard Bidan
     */
    public function test_skenario_11_pemeriksaan_kader_belum_divalidasi()
    {
        $warga = $this->createBalita('Balita Belum Validasi', 12);

        $pem = Pemeriksaan::create([
            'warga_id' => $warga->id,
            'sesi_posyandu_id' => $this->sesi->id,
            'berat_badan' => 6.0,
            'tinggi_badan' => 68.0,
            'is_validated' => false, // Belum divalidasi
            'tipe_kohort' => 'balita',
        ]);

        KalkulasiZScore::create([
            'pemeriksaan_id' => $pem->id,
            'ews_status' => 'bahaya',
            'ews_message' => 'Kondisi Bahaya',
            'priority_score' => 85.0,
            'alert_status' => 'pending',
        ]);

        $response = $this->actingAs($this->bidan)->get(route('bidan.dashboard'));
        $response->assertStatus(200);
        $response->assertDontSee($warga->nama);
    }

    /**
     * Skenario 12: Pemeriksaan yang sudah divalidasi Bidan tampil di dashboard
     */
    public function test_skenario_12_pemeriksaan_yang_sudah_divalidasi_bidan()
    {
        $warga = $this->createBalita('Balita Tervalidasi', 12);

        $pem = Pemeriksaan::create([
            'warga_id' => $warga->id,
            'sesi_posyandu_id' => $this->sesi->id,
            'berat_badan' => 6.0,
            'tinggi_badan' => 68.0,
            'is_validated' => true, // Sudah divalidasi
            'tipe_kohort' => 'balita',
        ]);

        KalkulasiZScore::create([
            'pemeriksaan_id' => $pem->id,
            'ews_status' => 'bahaya',
            'ews_message' => 'Kondisi Bahaya Tervalidasi',
            'priority_score' => 88.0,
            'alert_status' => 'pending',
        ]);

        $response = $this->actingAs($this->bidan)->get(route('bidan.dashboard'));
        $response->assertStatus(200);
        $response->assertSee($warga->nama);
        $response->assertSee('Kondisi Bahaya Tervalidasi');
    }

    /**
     * Skenario 13: Pembuatan kunjungan rumah untuk status risiko tinggi
     */
    public function test_skenario_13_pembuatan_kunjungan_rumah_status_risiko_tinggi()
    {
        $warga = $this->createBalita('Balita Kunjungan Tinggi', 14);

        $ewsResult = [
            'status' => 'risiko_tinggi',
            'message' => 'Terindikasi Stunting',
            'priority_score' => 52.5,
        ];

        $visit = $this->ewsService->triggerKunjunganRumah($warga, $ewsResult, $this->bidan->id);

        $this->assertNotNull($visit);
        $this->assertEquals('tinggi', $visit->prioritas);
        $this->assertEquals('dijadwalkan', $visit->status);
        $this->assertDatabaseHas('kunjungan_rumahs', [
            'warga_id' => $warga->id,
            'prioritas' => 'tinggi',
        ]);
    }

    /**
     * Skenario 14: Pembuatan kunjungan rumah untuk status bahaya (Valid ENUM 'kritis')
     */
    public function test_skenario_14_pembuatan_kunjungan_rumah_status_bahaya()
    {
        $warga = $this->createBalita('Balita Kunjungan Bahaya', 10);

        $ewsResult = [
            'status' => 'bahaya',
            'message' => 'Gizi Buruk Akut',
            'priority_score' => 89.0,
        ];

        $visit = $this->ewsService->triggerKunjunganRumah($warga, $ewsResult, $this->bidan->id);

        $this->assertNotNull($visit);
        $this->assertEquals('kritis', $visit->prioritas); // Bukan string 'sangat_tinggi' ilegal
        $this->assertDatabaseHas('kunjungan_rumahs', [
            'warga_id' => $warga->id,
            'prioritas' => 'kritis',
        ]);
    }

    /**
     * Skenario 15: Pencegahan tiket kunjungan rumah ganda
     */
    public function test_skenario_15_pencegahan_tiket_kunjungan_rumah_ganda()
    {
        $warga = $this->createBalita('Balita Tiket Tunggal', 12);

        $ewsResult = ['status' => 'bahaya', 'message' => 'Bahaya 1', 'priority_score' => 90.0];

        $visit1 = $this->ewsService->triggerKunjunganRumah($warga, $ewsResult);
        $this->assertNotNull($visit1);

        // Percobaan pemicu kedua saat kunjungan pertama masih 'dijadwalkan'
        $visit2 = $this->ewsService->triggerKunjunganRumah($warga, $ewsResult);
        $this->assertNull($visit2);

        $this->assertEquals(1, KunjunganRumah::where('warga_id', $warga->id)->count());
    }

    /**
     * Skenario 16: Alert yang sudah ditindaklanjuti hilang dari antrean aktif dashboard
     */
    public function test_skenario_16_alert_yang_sudah_ditindaklanjuti_hilang_dari_antrean_aktif()
    {
        $warga = $this->createBalita('Balita Selesai Intervensi', 14);

        $pem = Pemeriksaan::create([
            'warga_id' => $warga->id,
            'sesi_posyandu_id' => $this->sesi->id,
            'berat_badan' => 7.5,
            'tinggi_badan' => 74.0,
            'is_validated' => true,
            'tipe_kohort' => 'balita',
        ]);

        $kalkulasi = KalkulasiZScore::create([
            'pemeriksaan_id' => $pem->id,
            'ews_status' => 'bahaya',
            'ews_message' => 'Perlu Rujukan Segera',
            'priority_score' => 84.0,
            'alert_status' => 'pending',
        ]);

        // Sebelum resolusi: Tampil di antrean aktif dashboard
        $responseBefore = $this->actingAs($this->bidan)->get(route('bidan.dashboard'));
        $responseBefore->assertSee($warga->nama);

        // Bidan melakukan tindak lanjut medis
        $this->ewsService->resolveAlert($pem, 'Dirujuk ke Puskesmas', 'Diberikan PMT Biskuit', $this->bidan->id);

        $kalkulasi->refresh();
        $this->assertEquals('resolved', $kalkulasi->alert_status);
        $this->assertNotNull($kalkulasi->resolved_at);

        // Setelah resolusi: Hilang dari antrean aktif default dashboard
        $responseAfter = $this->actingAs($this->bidan)->get(route('bidan.dashboard'));
        $responseAfter->assertDontSee($warga->nama);

        // Namun tetap dapat dilihat di filter riwayat 'resolved'
        $responseResolved = $this->actingAs($this->bidan)->get(route('bidan.dashboard', ['alert_filter' => 'resolved']));
        $responseResolved->assertSee($warga->nama);
    }

    /**
     * Skenario 17: Alert baru setelah alert sebelumnya diselesaikan
     */
    public function test_skenario_17_alert_baru_setelah_alert_sebelumnya_diselesaikan()
    {
        $warga = $this->createBalita('Balita Riwayat Alert', 12);
        $sesiLalu = $this->createSesi(today()->subMonth());

        // Sesi bulan lalu: Pemeriksaan 1 (Diselesaikan)
        $pem1 = Pemeriksaan::create([
            'warga_id' => $warga->id,
            'sesi_posyandu_id' => $sesiLalu->id,
            'berat_badan' => 8.0,
            'is_validated' => true,
            'tipe_kohort' => 'balita',
            'created_at' => now()->subMonth(),
        ]);
        KalkulasiZScore::create([
            'pemeriksaan_id' => $pem1->id,
            'ews_status' => 'risiko_tinggi',
            'alert_status' => 'resolved',
            'resolved_at' => now()->subMonth(),
        ]);

        // Sesi sekarang: Pemeriksaan 2 (Alert baru independen)
        $pem2 = Pemeriksaan::create([
            'warga_id' => $warga->id,
            'sesi_posyandu_id' => $this->sesi->id,
            'berat_badan' => 7.8,
            'is_validated' => true,
            'tipe_kohort' => 'balita',
        ]);
        $res2 = $this->ewsService->evaluate($warga, -2.5, -1.0, -1.2, 7.8, $pem2->id);

        $kalkulasi2 = KalkulasiZScore::create([
            'pemeriksaan_id' => $pem2->id,
            'ews_status' => $res2['status'],
            'ews_message' => $res2['message'],
            'priority_score' => $res2['priority_score'],
            'alert_status' => $res2['alert_status'],
        ]);

        $this->assertEquals('pending', $kalkulasi2->alert_status);
        $this->assertEquals(2, KalkulasiZScore::whereHas('pemeriksaan', fn($q) => $q->where('warga_id', $warga->id))->count());
    }

    /**
     * Skenario 18: Dua atau lebih balita dalam kategori risiko yang sama tetapi mempunyai tingkat urgensi berbeda
     * Validasi: Multi-criteria priority score membedakan dan mengurutkan berdasarkan keparahan
     */
    public function test_skenario_18_pemeringkatan_prioritas_urgensi_berbeda()
    {
        // Balita A: Kategori Bahaya terisolasi (hanya HAZ = -3.1, indikator lain normal)
        $wargaA = $this->createBalita('Balita A (Stunting Saja)', 22);
        $pemA = Pemeriksaan::create(['warga_id' => $wargaA->id, 'sesi_posyandu_id' => $this->sesi->id, 'berat_badan' => 10.5, 'is_validated' => true, 'tipe_kohort' => 'balita']);
        $resA = $this->ewsService->evaluate($wargaA, -0.5, -3.1, -0.2, 10.5, $pemA->id);
        KalkulasiZScore::create([
            'pemeriksaan_id' => $pemA->id,
            'ews_status' => $resA['status'],
            'priority_score' => $resA['priority_score'],
            'alert_status' => 'pending',
            'created_at' => now(), // Waktu sekarang
        ]);

        // Balita B: Kategori Bahaya kritis komposit (WHZ = -4.2 + HAZ = -3.5 + 3T)
        $wargaB = $this->createBalita('Balita B (Komplikasi Kritis)', 10);
        $sesiB3 = $this->createSesi(today()->subMonths(3));
        $sesiB2 = $this->createSesi(today()->subMonths(2));
        $sesiB1 = $this->createSesi(today()->subMonth());

        Pemeriksaan::create(['warga_id' => $wargaB->id, 'sesi_posyandu_id' => $sesiB3->id, 'berat_badan' => 7.0, 'is_validated' => true, 'tipe_kohort' => 'balita', 'created_at' => now()->subMonths(3)]);
        Pemeriksaan::create(['warga_id' => $wargaB->id, 'sesi_posyandu_id' => $sesiB2->id, 'berat_badan' => 6.8, 'is_validated' => true, 'tipe_kohort' => 'balita', 'created_at' => now()->subMonths(2)]);
        Pemeriksaan::create(['warga_id' => $wargaB->id, 'sesi_posyandu_id' => $sesiB1->id, 'berat_badan' => 6.5, 'is_validated' => true, 'tipe_kohort' => 'balita', 'created_at' => now()->subMonth()]);
        
        $pemB = Pemeriksaan::create(['warga_id' => $wargaB->id, 'sesi_posyandu_id' => $this->sesi->id, 'berat_badan' => 6.2, 'is_validated' => true, 'tipe_kohort' => 'balita', 'created_at' => now()->subHour()]);
        $resB = $this->ewsService->evaluate($wargaB, -2.8, -3.5, -4.2, 6.2, $pemB->id);
        KalkulasiZScore::create([
            'pemeriksaan_id' => $pemB->id,
            'ews_status' => $resB['status'],
            'priority_score' => $resB['priority_score'],
            'alert_status' => 'pending',
            'created_at' => now()->subHour(), // Dibuat 1 jam lebih dulu dari A
        ]);

        // Keduanya masuk kategori 'bahaya'
        $this->assertEquals('bahaya', $resA['status']);
        $this->assertEquals('bahaya', $resB['status']);

        // Namun Balita B memiliki skor prioritas jauh lebih tinggi dari Balita A
        $this->assertGreaterThan($resA['priority_score'], $resB['priority_score']);

        // Pada query dashboard, Balita B harus berada di urutan PERTAMA melompati Balita A meskipun Balita A dicatat lebih baru
        $response = $this->actingAs($this->bidan)->get(route('bidan.dashboard'));
        $alerts = $response->viewData('ewsAlerts');

        $this->assertCount(2, $alerts);
        $this->assertEquals($wargaB->id, $alerts->first()->pemeriksaan->warga_id);
        $this->assertEquals($wargaA->id, $alerts->last()->pemeriksaan->warga_id);
    }
}
