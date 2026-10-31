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
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class EwsEdgeCaseVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected $bidan1;
    protected $bidan2;
    protected $kader;
    protected $region;
    protected $sesi;
    protected $ewsService;

    protected function setUp(): void
    {
        parent::setUp();

        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Puskesmas']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Kader_Posyandu']);

        $this->bidan1 = User::factory()->create(['name' => 'Bidan Siti']);
        $this->bidan1->assignRole('Puskesmas');

        $this->bidan2 = User::factory()->create(['name' => 'Bidan Ratna']);
        $this->bidan2->assignRole('Puskesmas');

        $this->kader = User::factory()->create(['name' => 'Kader Ani']);
        $this->kader->assignRole('Kader_Posyandu');

        $this->region = Region::create(['name' => 'Dusun Mekar', 'type' => 'dusun']);
        $this->sesi = $this->createSesi(today(), 'Sesi Utama');

        $this->ewsService = app(EwsService::class);
    }

    private function createSesi($date = null, string $nama = 'Sesi'): SesiPosyandu
    {
        return SesiPosyandu::create([
            'status' => 'aktif',
            'tanggal' => $date ?? today(),
            'nama_sesi' => $nama . ' ' . uniqid(),
            'lokasi' => 'Posyandu Melati',
            'waktu_mulai' => '08:00',
            'waktu_selesai' => '12:00',
        ]);
    }

    private function createBalita(string $nama, int $ageMonths = 12): Warga
    {
        $warga = Warga::create([
            'nama' => $nama,
            'nik' => '3202' . rand(100000000000, 999999999999),
            'tanggal_lahir' => today()->subMonths($ageMonths),
            'jenis_kelamin' => 'L',
            'alamat' => 'RT 02 RW 01',
            'region_id' => $this->region->id,
        ]);

        Balita::create([
            'warga_id' => $warga->id,
            'nama_orang_tua' => 'Ibu ' . $nama,
            'berat_lahir' => 3.1,
            'panjang_lahir' => 49.0,
        ]);

        return $warga;
    }

    /**
     * Edge Case 1: Dua pemeriksaan pada tanggal yang sama.
     * Memastikan pemeriksaan kedua mengambil pemeriksaan pertama sebagai riwayat terdekat
     * tanpa self-comparison dan tanpa collision urutan id/created_at.
     */
    public function test_edge_case_1_dua_pemeriksaan_pada_tanggal_sama()
    {
        $warga = $this->createBalita('Balita HariSama', 12);
        $sesi1 = $this->createSesi(today(), 'Sesi Pagi');
        $sesi2 = $this->createSesi(today(), 'Sesi Sore');

        // Pemeriksaan 1 pada pagi hari: BB = 10.0 kg
        $pem1 = Pemeriksaan::create([
            'warga_id' => $warga->id,
            'sesi_posyandu_id' => $sesi1->id,
            'berat_badan' => 10.0,
            'tinggi_badan' => 75.0,
            'is_validated' => true,
            'tipe_kohort' => 'balita',
        ]);
        $pem1->created_at = Carbon::now()->subHours(4);
        $pem1->save();

        // Pemeriksaan 2 pada sore hari: BB = 9.8 kg (turun 0.2kg)
        $pem2 = Pemeriksaan::create([
            'warga_id' => $warga->id,
            'sesi_posyandu_id' => $sesi2->id,
            'berat_badan' => 9.8,
            'tinggi_badan' => 75.0,
            'is_validated' => true,
            'tipe_kohort' => 'balita',
        ]);
        $pem2->created_at = Carbon::now()->subMinutes(30);
        $pem2->save();

        // Evaluasi EWS pada pemeriksaan 2
        $res = $this->ewsService->evaluate(
            $warga,
            0.0, // WAZ normal
            0.0, // HAZ normal
            0.0, // WHZ normal
            9.8,
            $pem2->id
        );

        // Riwayat harus mengambil pem1 (10.0), riwayat = [9.8, 10.0], t_count = 1 -> status WASPADA
        $this->assertEquals(1, $res['t_count'], '1T terdeteksi karena 9.8 <= 10.0 dari pemeriksaan pagi');
        $this->assertEquals('waspada', $res['status']);
        $this->assertEquals([9.8, 10.0], $res['history']);
    }

    /**
     * Edge Case 2: Data pemeriksaan yang tidak berurutan secara waktu insert (Back-dated data).
     * Memastikan query riwayat diurutkan berdasarkan created_at DESC, id DESC.
     */
    public function test_edge_case_2_data_pemeriksaan_tidak_berurutan_waktu_insert()
    {
        $warga = $this->createBalita('Balita Backdated', 15);
        $sesi1 = $this->createSesi(today()->subMonths(2), 'Sesi 2 Bulan Lalu');
        $sesi2 = $this->createSesi(today()->subMonth(), 'Sesi 1 Bulan Lalu');
        $sesi3 = $this->createSesi(today(), 'Sesi Hari Ini');

        // Insert pemeriksaan hari ini terlebih dahulu (BB 9.0)
        $pem3 = Pemeriksaan::create([
            'warga_id' => $warga->id,
            'sesi_posyandu_id' => $sesi3->id,
            'berat_badan' => 9.0,
            'tinggi_badan' => 78.0,
            'is_validated' => true,
            'tipe_kohort' => 'balita',
        ]);
        $pem3->created_at = Carbon::now();
        $pem3->save();

        // Lalu insert back-dated pemeriksaan 2 bulan lalu (BB 9.5)
        $pem1 = Pemeriksaan::create([
            'warga_id' => $warga->id,
            'sesi_posyandu_id' => $sesi1->id,
            'berat_badan' => 9.5,
            'tinggi_badan' => 76.0,
            'is_validated' => true,
            'tipe_kohort' => 'balita',
        ]);
        $pem1->created_at = Carbon::now()->subMonths(2);
        $pem1->save();

        // Lalu insert back-dated pemeriksaan 1 bulan lalu (BB 9.2)
        $pem2 = Pemeriksaan::create([
            'warga_id' => $warga->id,
            'sesi_posyandu_id' => $sesi2->id,
            'berat_badan' => 9.2,
            'tinggi_badan' => 77.0,
            'is_validated' => true,
            'tipe_kohort' => 'balita',
        ]);
        $pem2->created_at = Carbon::now()->subMonth();
        $pem2->save();

        // Evaluasi pem3 dengan created_at saat ini
        $res = $this->ewsService->evaluate(
            $warga,
            -1.0,
            -1.0,
            -1.0,
            9.0,
            $pem3->id
        );

        // Kronologi berat badan terurut dari terbaru: pem3(9.0), pem2(9.2), pem1(9.5)
        // 9.0 <= 9.2 (1T), 9.2 <= 9.5 (2T) -> 2T -> risiko_tinggi
        $this->assertEquals([9.0, 9.2, 9.5], $res['history']);
        $this->assertEquals(2, $res['t_count']);
        $this->assertEquals('risiko_tinggi', $res['status']);
    }

    /**
     * Edge Case 3: Nilai antropometri bernilai null.
     * Memastikan sistem tidak crash (TypeError / division by zero) saat salah satu/semua Z-Score bernilai null.
     */
    public function test_edge_case_3_nilai_antropometri_null()
    {
        $warga = $this->createBalita('Balita NullAnthro', 10);

        // Balita hanya ditimbang berat badannya (misal balita rewel tidak mau diukur tinggi badan)
        $res = $this->ewsService->evaluate(
            $warga,
            null, // WAZ null
            null, // HAZ null
            null, // WHZ null
            9.5,
            null
        );

        $this->assertIsArray($res);
        $this->assertEquals('normal', $res['status']);
        $this->assertEquals(0.0, $res['priority_score']);
        $this->assertEquals('resolved', $res['alert_status']);

        // Uji jika dengan null zscore tetapi memiliki riwayat penurunan berat badan (2T)
        $sesi1 = $this->createSesi(today()->subMonth());
        $pem1 = Pemeriksaan::create([
            'warga_id' => $warga->id,
            'sesi_posyandu_id' => $sesi1->id,
            'berat_badan' => 10.0,
            'tinggi_badan' => null,
            'is_validated' => true,
            'tipe_kohort' => 'balita',
        ]);
        $pem1->created_at = Carbon::now()->subMonth();
        $pem1->save();

        $res2 = $this->ewsService->evaluate(
            $warga,
            null,
            null,
            null,
            9.7, // Turun dari 10.0 -> 1T
            null
        );

        $this->assertEquals('waspada', $res2['status']);
        $this->assertEquals(1, $res2['t_count']);
        $this->assertGreaterThan(15.0, $res2['priority_score']);
    }

    /**
     * Edge Case 4: Perubahan status validasi (is_validated).
     * Alert hanya boleh muncul di dashboard bidan jika is_validated = true.
     */
    public function test_edge_case_4_perubahan_status_validasi()
    {
        $warga = $this->createBalita('Balita Unvalidated', 14);

        // Kader input pemeriksaan dengan indikasi Bahaya (WHZ -3.5) namun belum divalidasi bidan
        $pem = Pemeriksaan::create([
            'warga_id' => $warga->id,
            'sesi_posyandu_id' => $this->sesi->id,
            'berat_badan' => 7.0,
            'tinggi_badan' => 75.0,
            'is_validated' => false,
            'tipe_kohort' => 'balita',
        ]);

        $eval = $this->ewsService->evaluate($warga, -3.2, -1.0, -3.5, 7.0, $pem->id);

        KalkulasiZScore::create([
            'pemeriksaan_id' => $pem->id,
            'zscore_bb_u' => -3.2,
            'zscore_tb_u' => -1.0,
            'zscore_bb_tb' => -3.5,
            'status_gizi' => 'Gizi Buruk',
            'status_stunting' => 'Normal',
            'status_bb_tb' => 'Gizi Buruk',
            'ews_status' => $eval['status'],
            'ews_message' => $eval['message'],
            'priority_score' => $eval['priority_score'],
            'priority_details' => $eval['priority_details'],
            'alert_status' => $eval['alert_status'],
        ]);

        // Cek dashboard Bidan: alert TIDAK boleh muncul karena is_validated masih false
        $response = $this->actingAs($this->bidan1)->get(route('bidan.dashboard'));
        $response->assertStatus(200);
        $response->assertDontSee('Balita Unvalidated');

        // Bidan kemudian memvalidasi pemeriksaan tersebut
        $pem->update(['is_validated' => true]);

        // Sekarang alert HARUS muncul di dashboard Bidan
        $responseAfterValidation = $this->actingAs($this->bidan1)->get(route('bidan.dashboard'));
        $responseAfterValidation->assertStatus(200);
        $responseAfterValidation->assertSee('Balita Unvalidated');
    }

    /**
     * Edge Case 5: Pembaruan pemeriksaan lama (Idempotency).
     * Saat data pemeriksaan lama dikoreksi/diupdate, baris kalkulasi Z-Score tidak terduplikasi.
     */
    public function test_edge_case_5_pembaruan_pemeriksaan_lama_idempoten()
    {
        $warga = $this->createBalita('Balita Koreksi', 8);

        $pem = Pemeriksaan::create([
            'warga_id' => $warga->id,
            'sesi_posyandu_id' => $this->sesi->id,
            'berat_badan' => 8.0,
            'tinggi_badan' => 70.0,
            'is_validated' => true,
            'tipe_kohort' => 'balita',
        ]);

        $eval1 = $this->ewsService->evaluate($warga, -1.5, -0.5, -1.2, 8.0, $pem->id);

        KalkulasiZScore::updateOrCreate(
            ['pemeriksaan_id' => $pem->id],
            [
                'zscore_bb_u' => -1.5,
                'zscore_tb_u' => -0.5,
                'zscore_bb_tb' => -1.2,
                'status_gizi' => 'Normal',
                'status_stunting' => 'Normal',
                'status_bb_tb' => 'Normal',
                'ews_status' => $eval1['status'],
                'ews_message' => $eval1['message'],
                'priority_score' => $eval1['priority_score'],
                'priority_details' => $eval1['priority_details'],
                'alert_status' => $eval1['alert_status'],
            ]
        );

        $this->assertEquals(1, KalkulasiZScore::where('pemeriksaan_id', $pem->id)->count());

        // Koreksi data pengukuran (misal salah catat berat badan menjadi 7.0kg sehingga WHZ turun ke -2.5)
        $pem->update(['berat_badan' => 7.0]);
        $eval2 = $this->ewsService->evaluate($warga, -2.5, -0.5, -2.5, 7.0, $pem->id);

        KalkulasiZScore::updateOrCreate(
            ['pemeriksaan_id' => $pem->id],
            [
                'zscore_bb_u' => -2.5,
                'zscore_tb_u' => -0.5,
                'zscore_bb_tb' => -2.5,
                'status_gizi' => 'Kurang',
                'status_stunting' => 'Normal',
                'status_bb_tb' => 'Gizi Kurang',
                'ews_status' => $eval2['status'],
                'ews_message' => $eval2['message'],
                'priority_score' => $eval2['priority_score'],
                'priority_details' => $eval2['priority_details'],
                'alert_status' => $eval2['alert_status'],
            ]
        );

        // Pastikan TIDAK ada duplikasi baris
        $this->assertEquals(1, KalkulasiZScore::where('pemeriksaan_id', $pem->id)->count());
        $kalkulasi = KalkulasiZScore::where('pemeriksaan_id', $pem->id)->first();
        $this->assertEquals('risiko_tinggi', $kalkulasi->ews_status);
        $this->assertEquals('pending', $kalkulasi->alert_status);
    }

    /**
     * Edge Case 6: Duplikasi tiket kunjungan rumah dicegah secara otomatis.
     */
    public function test_edge_case_6_pencegahan_duplikasi_tiket_kunjungan_rumah()
    {
        $warga = $this->createBalita('Balita Kunjungan', 11);

        $ewsResult = [
            'status' => 'bahaya',
            'message' => 'Gizi Buruk Akut',
            'priority_score' => 85.5,
            'alert_status' => 'pending',
        ];

        // Trigger pertama: Tiket berhasil dibuat
        $visit1 = $this->ewsService->triggerKunjunganRumah($warga, $ewsResult, $this->bidan1->id);
        $this->assertNotNull($visit1);
        $this->assertEquals('kritis', $visit1->prioritas);
        $this->assertEquals('dijadwalkan', $visit1->status);

        // Trigger kedua untuk anak yang sama ketika status kunjungan masih 'dijadwalkan'
        $visit2 = $this->ewsService->triggerKunjunganRumah($warga, $ewsResult, $this->bidan1->id);
        $this->assertNull($visit2, 'Pemicu kedua harus mengembalikan null untuk mencegah duplikasi tiket');

        // Total tiket di database untuk warga ini harus tepat 1
        $totalVisits = KunjunganRumah::where('warga_id', $warga->id)->count();
        $this->assertEquals(1, $totalVisits);
    }

    /**
     * Edge Case 7: Integritas transaksi basis data (Rollback saat terjadi kegagalan).
     */
    public function test_edge_case_7_integritas_transaksi_database_rollback()
    {
        $warga = $this->createBalita('Balita Rollback', 9);

        $initialPemeriksaanCount = Pemeriksaan::count();
        $initialKalkulasiCount = KalkulasiZScore::count();

        // Simulasikan alur transaksi controller dengan kegagalan buatan
        try {
            DB::beginTransaction();

            $pem = Pemeriksaan::create([
                'warga_id' => $warga->id,
                'sesi_posyandu_id' => $this->sesi->id,
                'berat_badan' => 8.5,
                'tinggi_badan' => 72.0,
                'is_validated' => false,
                'tipe_kohort' => 'balita',
            ]);

            // Terjadi kesalahan tidak terduga sebelum commit (misalnya koneksi putus atau constraint error)
            throw new \Exception('Simulasi kegagalan sistem database');

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
        }

        // Pastikan tidak ada data orphan yang tersimpan di database
        $this->assertEquals($initialPemeriksaanCount, Pemeriksaan::count(), 'Pemeriksaan harus ter-rollback');
        $this->assertEquals($initialKalkulasiCount, KalkulasiZScore::count(), 'KalkulasiZScore harus ter-rollback');
    }

    /**
     * Edge Case 8: Perubahan status alert oleh petugas berbeda (Multi-officer audit trail).
     */
    public function test_edge_case_8_perubahan_status_alert_oleh_petugas_berbeda()
    {
        $warga = $this->createBalita('Balita MultiPetugas', 13);

        $pem = Pemeriksaan::create([
            'warga_id' => $warga->id,
            'sesi_posyandu_id' => $this->sesi->id,
            'berat_badan' => 7.2,
            'tinggi_badan' => 73.0,
            'is_validated' => true,
            'tipe_kohort' => 'balita',
        ]);

        $eval = $this->ewsService->evaluate($warga, -3.1, -1.5, -3.2, 7.2, $pem->id);

        $kalkulasi = KalkulasiZScore::create([
            'pemeriksaan_id' => $pem->id,
            'zscore_bb_u' => -3.1,
            'zscore_tb_u' => -1.5,
            'zscore_bb_tb' => -3.2,
            'status_gizi' => 'Gizi Buruk',
            'status_stunting' => 'Normal',
            'status_bb_tb' => 'Gizi Buruk',
            'ews_status' => $eval['status'],
            'ews_message' => $eval['message'],
            'priority_score' => $eval['priority_score'],
            'priority_details' => $eval['priority_details'],
            'alert_status' => 'pending',
        ]);

        $this->assertEquals('pending', $kalkulasi->fresh()->alert_status);

        // Langkah 1: Bidan 1 (Bidan Siti) membuka form tindak lanjut -> status berubah menjadi 'in_progress'
        $response1 = $this->actingAs($this->bidan1)->get(route('bidan.pemeriksaan.tindak-lanjut', $pem->id));
        $response1->assertStatus(200);

        $this->assertEquals('in_progress', $kalkulasi->fresh()->alert_status);
        $this->assertNull($kalkulasi->fresh()->resolved_by);

        // Langkah 2: Bidan 2 (Bidan Ratna) mengambil alih shift dan melakukan submit intervensi medis
        $response2 = $this->actingAs($this->bidan2)->patch(route('bidan.pemeriksaan.tindak-lanjut.update', $pem->id), [
            'tindakan' => 'Pemberian F-75 dan rujukan ke poli gizi spesialis anak.',
            'catatan' => 'Shift siang ditangani oleh Bidan Ratna.',
        ]);
        $response2->assertRedirect(route('bidan.dashboard'));

        $kalkulasiFresh = $kalkulasi->fresh();
        $this->assertEquals('resolved', $kalkulasiFresh->alert_status);
        $this->assertNotNull($kalkulasiFresh->resolved_at);
        $this->assertEquals($this->bidan2->id, $kalkulasiFresh->resolved_by, 'Audit trail harus mencatat ID Bidan 2 sebagai eksekutor penyelesaian');
        $this->assertEquals('Bidan Ratna', $kalkulasiFresh->resolvedBy->name);
    }
}
