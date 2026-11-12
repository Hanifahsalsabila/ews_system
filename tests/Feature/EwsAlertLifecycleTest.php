<?php

namespace Tests\Feature;

use App\Models\Balita;
use App\Models\KalkulasiZScore;
use App\Models\KunjunganRumah;
use App\Models\Pemeriksaan;
use App\Models\SesiPosyandu;
use App\Models\User;
use App\Models\Warga;
use App\Services\EwsService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EwsAlertLifecycleTest extends TestCase
{
    use RefreshDatabase;

    protected User $bidan;
    protected User $kader;
    protected Warga $warga;
    protected Balita $balita;
    protected SesiPosyandu $sesi;
    protected EwsService $ewsService;
    protected $region;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->seed(\Database\Seeders\WhoAnthroV110Seeder::class);

        $this->ewsService = app(EwsService::class);


        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Puskesmas']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Kader_Posyandu']);

        // Buat user Bidan dan Kader
        $this->bidan = User::factory()->create(['name' => 'Bidan Siti, S.Tr.Keb']);
        $this->bidan->assignRole('Puskesmas');

        $this->kader = User::factory()->create(['name' => 'Kader Ani']);
        $this->kader->assignRole('Kader_Posyandu');

        $this->region = \App\Models\Region::create(['name' => 'Dusun 01', 'type' => 'dusun']);

        // Buat Sesi Posyandu aktif
        $this->sesi = SesiPosyandu::create([
            'nama_sesi' => 'Posyandu Mawar',
            'tanggal' => Carbon::now()->subDays(2)->toDateString(),
            'waktu_mulai' => '08:00',
            'waktu_selesai' => '12:00',
            'lokasi' => 'Balai RW 01',
            'status' => 'aktif',
        ]);

        // Buat Warga balita umur 12 bulan (Laki-laki)
        $this->warga = Warga::create([
            'nik' => '3201012345678901',
            'nama' => 'Balita Uji Lifecycle',
            'tanggal_lahir' => Carbon::parse($this->sesi->tanggal)->subMonths(12)->toDateString(),
            'jenis_kelamin' => 'L',
            'alamat' => 'Jl. Kesehatan No. 10',
            'region_id' => $this->region->id,
        ]);

        $this->balita = Balita::create([
            'warga_id' => $this->warga->id,
            'nama_orang_tua' => 'Ibu Kandung',
            'berat_lahir' => 3.2,
            'panjang_lahir' => 49.0,
        ]);
    }

    /**
     * Helper untuk membuat pemeriksaan dasar
     */
    protected function createPemeriksaan(array $attributes = []): Pemeriksaan
    {
        return Pemeriksaan::create(array_merge([
            'warga_id' => $this->warga->id,
            'sesi_posyandu_id' => $this->sesi->id,
            'tipe_kohort' => 'balita',
            'berat_badan' => 6.5,
            'tinggi_badan' => 70.0,
            'posisi_ukur' => 'terlentang',
            'is_validated' => true,
            'petugas_id' => $this->kader->id,
        ], $attributes));
    }

    /**
     * 1. Alert pending tidak otomatis resolved ketika hasil evaluasi menjadi normal.
     */
    public function test_alert_pending_tidak_otomatis_resolved_ketika_hasil_evaluasi_menjadi_normal(): void
    {
        // Setup: Pemeriksaan awal dengan status gizi buruk (bahaya) -> alert_status pending
        $pemeriksaan = $this->createPemeriksaan([
            'berat_badan' => 5.5, // Sangat kurang
            'tinggi_badan' => 70.0,
        ]);

        $evalBahaya = $this->ewsService->evaluate(
            $this->warga,
            -3.5, // zBbu
            -1.0, // zTbu
            -3.2, // zBbTb
            5.5,
            $pemeriksaan->id,
            $this->warga->tanggal_lahir,
            $this->sesi->tanggal
        );

        $this->assertEquals('bahaya', $evalBahaya['status']);

        // Sinkronisasi awal: alert_status harus 'pending'
        $kalkulasi = $this->ewsService->syncKalkulasiZScore($pemeriksaan, [
            'zscore_bb_u' => -3.5,
            'zscore_tb_u' => -1.0,
            'zscore_bb_tb' => -3.2,
            'status_gizi' => 'Gizi Buruk',
            'status_stunting' => 'Normal',
            'status_bb_tb' => 'Gizi Buruk',
            'age_in_days' => 365,
        ], $evalBahaya);

        $this->assertEquals('pending', $kalkulasi->alert_status);
        $this->assertNull($kalkulasi->resolved_at);
        $this->assertNull($kalkulasi->resolved_by);

        // Simulasi: Pemeriksaan yang sama dievaluasi ulang dengan status normal
        $evalNormal = $this->ewsService->evaluate(
            $this->warga,
            0.1,  // zBbu normal
            0.2,  // zTbu normal
            0.0,  // zBbTb normal
            9.5,
            $pemeriksaan->id,
            $this->warga->tanggal_lahir,
            $this->sesi->tanggal
        );

        $this->assertEquals('normal', $evalNormal['status']);

        // Sinkronisasi ulang data normal
        $kalkulasiUpdated = $this->ewsService->syncKalkulasiZScore($pemeriksaan, [
            'zscore_bb_u' => 0.1,
            'zscore_tb_u' => 0.2,
            'zscore_bb_tb' => 0.0,
            'status_gizi' => 'Gizi Baik',
            'status_stunting' => 'Normal',
            'status_bb_tb' => 'Gizi Baik',
            'age_in_days' => 365,
        ], $evalNormal);

        // ASSERTION WAJIB: ews_status ter-update normal, tetapi alert_status TETAP 'pending'
        $this->assertEquals('normal', $kalkulasiUpdated->ews_status);
        $this->assertEquals('pending', $kalkulasiUpdated->alert_status, 'Alert pending tidak boleh otomatis resolved ketika evaluasi menjadi normal');
        $this->assertNull($kalkulasiUpdated->resolved_at, 'resolved_at harus tetap null');
        $this->assertNull($kalkulasiUpdated->resolved_by, 'resolved_by harus tetap null');
    }

    /**
     * 2. Alert pending tidak otomatis resolved ketika data menjadi tidak lengkap.
     */
    public function test_alert_pending_tidak_otomatis_resolved_ketika_data_menjadi_tidak_lengkap(): void
    {
        $pemeriksaan = $this->createPemeriksaan([
            'berat_badan' => 6.0,
            'tinggi_badan' => 70.0,
        ]);

        $evalWaspada = $this->ewsService->evaluate(
            $this->warga,
            -2.2,
            -0.5,
            -1.8,
            6.0,
            $pemeriksaan->id,
            $this->warga->tanggal_lahir,
            $this->sesi->tanggal
        );

        $kalkulasi = $this->ewsService->syncKalkulasiZScore($pemeriksaan, [
            'zscore_bb_u' => -2.2,
            'zscore_tb_u' => -0.5,
            'zscore_bb_tb' => -1.8,
            'status_gizi' => 'Kurang',
            'status_stunting' => 'Normal',
            'status_bb_tb' => 'Normal',
            'age_in_days' => 365,
        ], $evalWaspada);

        $this->assertEquals('pending', $kalkulasi->alert_status);

        // Evaluasi ulang dengan data tidak lengkap (misal Z-Score TB/U dan BB/TB null)
        $evalIncomplete = $this->ewsService->evaluate(
            $this->warga,
            null,
            null,
            null,
            null,
            $pemeriksaan->id,
            $this->warga->tanggal_lahir,
            $this->sesi->tanggal
        );

        $this->assertEquals('data_tidak_lengkap', $evalIncomplete['status']);

        $kalkulasiUpdated = $this->ewsService->syncKalkulasiZScore($pemeriksaan, [
            'zscore_bb_u' => null,
            'zscore_tb_u' => null,
            'zscore_bb_tb' => null,
            'status_gizi' => 'Data tidak lengkap',
            'status_stunting' => 'Data tidak lengkap',
            'status_bb_tb' => 'Data tidak lengkap',
            'age_in_days' => 365,
        ], $evalIncomplete);

        // ASSERTION WAJIB: alert_status TETAP 'pending'
        $this->assertEquals('data_tidak_lengkap', $kalkulasiUpdated->ews_status);
        $this->assertEquals('pending', $kalkulasiUpdated->alert_status, 'Alert pending tidak boleh otomatis resolved ketika data menjadi tidak lengkap');
        $this->assertNull($kalkulasiUpdated->resolved_at);
        $this->assertNull($kalkulasiUpdated->resolved_by);
    }

    /**
     * 3. Alert in_progress tetap in_progress ketika evaluasi ulang dilakukan.
     */
    public function test_alert_in_progress_tetap_in_progress_ketika_evaluasi_ulang_dilakukan(): void
    {
        $pemeriksaan = $this->createPemeriksaan();

        $eval = $this->ewsService->evaluate($this->warga, -2.5, -1.0, -2.2, 6.0, $pemeriksaan->id, $this->warga->tanggal_lahir, $this->sesi->tanggal);
        $kalkulasi = $this->ewsService->syncKalkulasiZScore($pemeriksaan, [
            'zscore_bb_u' => -2.5,
            'status_gizi' => 'Kurang',
            'status_stunting' => 'Normal',
            'status_bb_tb' => 'Gizi Kurang',
            'age_in_days' => 365,
        ], $eval);

        // Bidan membuka form tindak lanjut -> status beralih ke in_progress
        $kalkulasi->update(['alert_status' => 'in_progress']);
        $this->assertEquals('in_progress', $kalkulasi->fresh()->alert_status);

        // Skenario A: Evaluasi ulang menjadi normal
        $evalNormal = $this->ewsService->evaluate($this->warga, 0.0, 0.0, 0.0, 9.0, $pemeriksaan->id, $this->warga->tanggal_lahir, $this->sesi->tanggal);
        $kalkulasiA = $this->ewsService->syncKalkulasiZScore($pemeriksaan, [
            'zscore_bb_u' => 0.0,
            'status_gizi' => 'Normal',
            'status_stunting' => 'Normal',
            'status_bb_tb' => 'Normal',
            'age_in_days' => 365,
        ], $evalNormal);

        $this->assertEquals('in_progress', $kalkulasiA->alert_status, 'in_progress harus tetap in_progress saat evaluasi normal');

        // Skenario B: Evaluasi ulang menjadi bahaya
        $evalBahaya = $this->ewsService->evaluate($this->warga, -3.5, -3.2, -3.1, 5.0, $pemeriksaan->id, $this->warga->tanggal_lahir, $this->sesi->tanggal);
        $kalkulasiB = $this->ewsService->syncKalkulasiZScore($pemeriksaan, [
            'zscore_bb_u' => -3.5,
            'status_gizi' => 'Gizi Buruk',
            'status_stunting' => 'Sangat Pendek',
            'status_bb_tb' => 'Gizi Buruk',
            'age_in_days' => 365,
        ], $evalBahaya);

        $this->assertEquals('in_progress', $kalkulasiB->alert_status, 'in_progress harus tetap in_progress saat evaluasi bahaya');

        // Skenario C: Evaluasi ulang menjadi data_tidak_lengkap
        $evalIncomplete = $this->ewsService->evaluate($this->warga, null, null, null, null, $pemeriksaan->id, $this->warga->tanggal_lahir, $this->sesi->tanggal);
        $kalkulasiC = $this->ewsService->syncKalkulasiZScore($pemeriksaan, [
            'zscore_bb_u' => null,
            'status_gizi' => 'Data tidak lengkap',
            'status_stunting' => 'Data tidak lengkap',
            'status_bb_tb' => 'Data tidak lengkap',
            'age_in_days' => 365,
        ], $evalIncomplete);

        $this->assertEquals('in_progress', $kalkulasiC->alert_status, 'in_progress harus tetap in_progress saat data tidak lengkap');
    }

    /**
     * 4. Alert resolved tidak otomatis kembali pending akibat evaluasi ulang.
     */
    public function test_alert_resolved_tidak_otomatis_kembali_pending_akibat_evaluasi_ulang(): void
    {
        $pemeriksaan = $this->createPemeriksaan();

        $evalBahaya = $this->ewsService->evaluate($this->warga, -3.2, -1.0, -3.1, 5.5, $pemeriksaan->id, $this->warga->tanggal_lahir, $this->sesi->tanggal);
        $this->ewsService->syncKalkulasiZScore($pemeriksaan, [
            'zscore_bb_u' => -3.2,
            'status_gizi' => 'Gizi Buruk',
            'status_stunting' => 'Normal',
            'status_bb_tb' => 'Gizi Buruk',
            'age_in_days' => 365,
        ], $evalBahaya);

        // Bidan menyelesaikan alert secara resmi
        $this->ewsService->resolveAlert(
            $pemeriksaan,
            'Pemberian RUTF & Edukasi PMBA',
            'Kondisi balita stabil, rujukan puskesmas disiapkan',
            $this->bidan->id
        );

        $kalkulasiResolved = $pemeriksaan->fresh()->kalkulasiZscore;
        $this->assertEquals('resolved', $kalkulasiResolved->alert_status);
        $this->assertNotNull($kalkulasiResolved->resolved_at);
        $this->assertEquals($this->bidan->id, $kalkulasiResolved->resolved_by);

        $originalResolvedAt = $kalkulasiResolved->resolved_at->toIso8601String();
        $originalResolvedBy = $kalkulasiResolved->resolved_by;

        // Simulasi evaluasi ulang pemeriksaan yang sama dengan hasil tetap bahaya
        $evalRecheck = $this->ewsService->evaluate($this->warga, -3.4, -1.0, -3.3, 5.4, $pemeriksaan->id, $this->warga->tanggal_lahir, $this->sesi->tanggal);
        $kalkulasiRechecked = $this->ewsService->syncKalkulasiZScore($pemeriksaan, [
            'zscore_bb_u' => -3.4,
            'status_gizi' => 'Gizi Buruk',
            'status_stunting' => 'Normal',
            'status_bb_tb' => 'Gizi Buruk',
            'age_in_days' => 365,
        ], $evalRecheck);

        // ASSERTION WAJIB: alert_status TETAP 'resolved', resolved_at dan resolved_by tidak hilang
        $this->assertEquals('resolved', $kalkulasiRechecked->alert_status, 'Alert resolved tidak boleh kembali pending saat evaluasi ulang');
        $this->assertEquals($originalResolvedAt, $kalkulasiRechecked->resolved_at->toIso8601String(), 'resolved_at audit trail harus dipertahankan');
        $this->assertEquals($originalResolvedBy, $kalkulasiRechecked->resolved_by, 'resolved_by audit trail harus dipertahankan');
    }

    /**
     * 5. Penyelesaian alert oleh Bidan tetap mencatat resolved_at dan resolved_by.
     */
    public function test_penyelesaian_alert_oleh_bidan_tetap_mencatat_resolved_at_dan_resolved_by(): void
    {
        $pemeriksaan = $this->createPemeriksaan();

        $eval = $this->ewsService->evaluate($this->warga, -2.5, -2.5, -1.5, 6.0, $pemeriksaan->id, $this->warga->tanggal_lahir, $this->sesi->tanggal);
        $this->ewsService->syncKalkulasiZScore($pemeriksaan, [
            'zscore_bb_u' => -2.5,
            'status_gizi' => 'Kurang',
            'status_stunting' => 'Pendek',
            'status_bb_tb' => 'Normal',
            'age_in_days' => 365,
        ], $eval);

        $beforeTime = Carbon::now()->subSecond();

        $result = $this->ewsService->resolveAlert(
            $pemeriksaan,
            'Konseling Gizi Terpadu & PMT Lokal',
            'Evaluasi 2 minggu ke depan',
            $this->bidan->id
        );

        $this->assertTrue($result);

        $freshPemeriksaan = $pemeriksaan->fresh();
        $freshKalkulasi = $freshPemeriksaan->kalkulasiZscore;

        $this->assertEquals('Konseling Gizi Terpadu & PMT Lokal', $freshPemeriksaan->tindakan);
        $this->assertEquals('Evaluasi 2 minggu ke depan', $freshPemeriksaan->catatan);

        $this->assertEquals('resolved', $freshKalkulasi->alert_status);
        $this->assertNotNull($freshKalkulasi->resolved_at);
        $this->assertTrue($freshKalkulasi->resolved_at->greaterThanOrEqualTo($beforeTime));
        $this->assertEquals($this->bidan->id, $freshKalkulasi->resolved_by);
        $this->assertEquals($this->bidan->name, $freshKalkulasi->resolvedBy->name);
    }

    /**
     * 6. Pemeriksaan baru dengan risiko yang sama tetap dapat menghasilkan alert baru sesuai aturan sistem.
     */
    public function test_pemeriksaan_baru_dengan_risiko_yang_sama_tetap_dapat_menghasilkan_alert_baru(): void
    {
        // Pemeriksaan 1 (Sesi Bulan Lalu): Mengalami risiko dan telah diselesaikan oleh Bidan
        $pemeriksaan1 = $this->createPemeriksaan([
            'sesi_posyandu_id' => $this->sesi->id,
            'berat_badan' => 5.5,
        ]);

        $eval1 = $this->ewsService->evaluate($this->warga, -3.2, -1.0, -3.1, 5.5, $pemeriksaan1->id, $this->warga->tanggal_lahir, $this->sesi->tanggal);
        $this->ewsService->syncKalkulasiZScore($pemeriksaan1, [
            'zscore_bb_u' => -3.2,
            'status_gizi' => 'Gizi Buruk',
            'status_stunting' => 'Normal',
            'status_bb_tb' => 'Gizi Buruk',
            'age_in_days' => 365,
        ], $eval1);

        $this->ewsService->resolveAlert($pemeriksaan1, 'Tindakan Sesi 1', 'Catatan Sesi 1', $this->bidan->id);

        $kalkulasi1 = $pemeriksaan1->fresh()->kalkulasiZscore;
        $this->assertEquals('resolved', $kalkulasi1->alert_status);
        $this->assertEquals($this->bidan->id, $kalkulasi1->resolved_by);

        // Buat Sesi Baru (Bulan Berikutnya)
        $sesiBaru = SesiPosyandu::create([
            'nama_sesi' => 'Posyandu Mawar Sesi 2',
            'tanggal' => Carbon::now()->toDateString(),
            'waktu_mulai' => '08:00',
            'waktu_selesai' => '12:00',
            'lokasi' => 'Balai RW 01',
            'status' => 'aktif',
        ]);

        // Pemeriksaan 2 (Sesi Baru): Balita masih berisiko
        $pemeriksaan2 = Pemeriksaan::create([
            'warga_id' => $this->warga->id,
            'sesi_posyandu_id' => $sesiBaru->id,
            'tipe_kohort' => 'balita',
            'berat_badan' => 5.6,
            'tinggi_badan' => 71.0,
            'posisi_ukur' => 'terlentang',
            'is_validated' => true,
            'petugas_id' => $this->kader->id,
        ]);

        $eval2 = $this->ewsService->evaluate($this->warga, -3.1, -1.0, -3.0, 5.6, $pemeriksaan2->id, $this->warga->tanggal_lahir, $sesiBaru->tanggal);
        $kalkulasi2 = $this->ewsService->syncKalkulasiZScore($pemeriksaan2, [
            'zscore_bb_u' => -3.1,
            'status_gizi' => 'Gizi Buruk',
            'status_stunting' => 'Normal',
            'status_bb_tb' => 'Gizi Buruk',
            'age_in_days' => 395,
        ], $eval2);

        // ASSERTION WAJIB:
        // Pemeriksaan 1 tetap resolved
        $this->assertEquals('resolved', $kalkulasi1->fresh()->alert_status);
        $this->assertEquals($this->bidan->id, $kalkulasi1->fresh()->resolved_by);

        // Pemeriksaan 2 BARU berstatus 'pending' (alert baru tercipta tanpa tertahan oleh riwayat resolved lama)
        $this->assertEquals('pending', $kalkulasi2->alert_status, 'Pemeriksaan baru dengan risiko harus menghasilkan alert pending baru');
        $this->assertNull($kalkulasi2->resolved_at);
        $this->assertNull($kalkulasi2->resolved_by);
    }

    /**
     * 7. Tidak terjadi duplikasi alert atau kunjungan rumah.
     */
    public function test_tidak_terjadi_duplikasi_alert_atau_kunjungan_rumah(): void
    {
        $pemeriksaan = $this->createPemeriksaan();

        $eval = $this->ewsService->evaluate($this->warga, -3.5, -1.0, -3.2, 5.5, $pemeriksaan->id, $this->warga->tanggal_lahir, $this->sesi->tanggal);

        // Panggilan sinkronisasi pertama
        $kalkulasi1 = $this->ewsService->syncKalkulasiZScore($pemeriksaan, [
            'zscore_bb_u' => -3.5,
            'status_gizi' => 'Gizi Buruk',
            'status_stunting' => 'Normal',
            'status_bb_tb' => 'Gizi Buruk',
            'age_in_days' => 365,
        ], $eval);

        // Pemicu kunjungan rumah pertama
        $kunjungan1 = $this->ewsService->triggerKunjunganRumah($this->warga, $eval, $this->bidan->id);
        $this->assertNotNull($kunjungan1);

        // Panggilan sinkronisasi kedua (misal update atau re-evaluasi)
        $kalkulasi2 = $this->ewsService->syncKalkulasiZScore($pemeriksaan, [
            'zscore_bb_u' => -3.5,
            'status_gizi' => 'Gizi Buruk',
            'status_stunting' => 'Normal',
            'status_bb_tb' => 'Gizi Buruk',
            'age_in_days' => 365,
        ], $eval);

        // Pemicu kunjungan rumah kedua
        $kunjungan2 = $this->ewsService->triggerKunjunganRumah($this->warga, $eval, $this->bidan->id);

        // ASSERTION WAJIB:
        // 1. Kalkulasi ID sama (tidak duplikasi baris database)
        $this->assertEquals($kalkulasi1->id, $kalkulasi2->id);
        $this->assertEquals(1, KalkulasiZScore::where('pemeriksaan_id', $pemeriksaan->id)->count());

        // 2. Kunjungan rumah kedua harus NULL (tidak membuat tiket duplikat)
        $this->assertNull($kunjungan2, 'Kunjungan rumah kedua harus dicegah karena sudah ada tiket aktif');
        $this->assertEquals(1, KunjunganRumah::where('warga_id', $this->warga->id)->count());
    }

    /**
     * 8. Data tidak lengkap tidak membuat tiket kunjungan rumah darurat.
     */
    public function test_data_tidak_lengkap_tidak_membuat_tiket_kunjungan_rumah_darurat(): void
    {
        $evalIncomplete = $this->ewsService->evaluate(
            $this->warga,
            null,
            null,
            null,
            null,
            null,
            $this->warga->tanggal_lahir,
            $this->sesi->tanggal
        );

        $this->assertEquals('data_tidak_lengkap', $evalIncomplete['status']);

        $kunjungan = $this->ewsService->triggerKunjunganRumah($this->warga, $evalIncomplete, $this->bidan->id);

        $this->assertNull($kunjungan, 'data_tidak_lengkap tidak boleh memicu kunjungan rumah');
        $this->assertEquals(0, KunjunganRumah::where('warga_id', $this->warga->id)->count());
    }

    /**
     * 9. Transisi record awal non-alert (normal) menjadi berisiko saat koreksi data membuka alert pending.
     */
    public function test_koreksi_data_dari_normal_menjadi_berisiko_membuka_alert_pending(): void
    {
        $pemeriksaan = $this->createPemeriksaan([
            'berat_badan' => 9.5,
            'tinggi_badan' => 74.0,
        ]);

        // Awalnya normal
        $evalNormal = $this->ewsService->evaluate($this->warga, 0.0, 0.0, 0.0, 9.5, $pemeriksaan->id, $this->warga->tanggal_lahir, $this->sesi->tanggal);
        $kalkulasiAwal = $this->ewsService->syncKalkulasiZScore($pemeriksaan, [
            'zscore_bb_u' => 0.0,
            'status_gizi' => 'Normal',
            'status_stunting' => 'Normal',
            'status_bb_tb' => 'Normal',
            'age_in_days' => 365,
        ], $evalNormal);

        // Record normal awal berstatus resolved tanpa bidan
        $this->assertEquals('resolved', $kalkulasiAwal->alert_status);
        $this->assertNull($kalkulasiAwal->resolved_by);

        // Terjadi koreksi data pengukuran (ternyata anak sangat kurus / bahaya)
        $evalBahaya = $this->ewsService->evaluate($this->warga, -3.5, 0.0, -3.2, 5.5, $pemeriksaan->id, $this->warga->tanggal_lahir, $this->sesi->tanggal);
        $kalkulasiKoreksi = $this->ewsService->syncKalkulasiZScore($pemeriksaan, [
            'zscore_bb_u' => -3.5,
            'status_gizi' => 'Gizi Buruk',
            'status_stunting' => 'Normal',
            'status_bb_tb' => 'Gizi Buruk',
            'age_in_days' => 365,
        ], $evalBahaya);

        // Alert HARUS terbuka (pending) karena muncul risiko baru yang belum ditangani Bidan
        $this->assertEquals('bahaya', $kalkulasiKoreksi->ews_status);
        $this->assertEquals('pending', $kalkulasiKoreksi->alert_status, 'Koreksi data dari normal ke bahaya harus membuka alert pending');
    }

    /**
     * 10. Pengujian integrasi alur HTTP nyata: Kader Store -> Bidan Koreksi Normal -> Bidan In Progress -> Bidan Resolve -> Bidan Re-evaluasi.
     */
    public function test_alur_lengkap_integrasi_siklus_alert_melalui_controller_kader_dan_bidan(): void
    {
        // Pastikan ada sesi aktif hari ini
        $sesiHariIni = SesiPosyandu::create([
            'nama_sesi' => 'Posyandu Aktif Hari Ini',
            'tanggal' => Carbon::today()->toDateString(),
            'waktu_mulai' => '08:00',
            'waktu_selesai' => '12:00',
            'lokasi' => 'Posyandu Mawar',
            'status' => 'aktif',
        ]);

        // Buat absensi warga pada sesi hari ini
        \App\Models\Absensi::create([
            'warga_id' => $this->warga->id,
            'sesi_posyandu_id' => $sesiHariIni->id,
            'jam_kehadiran' => '08:30:00',
            'status' => 'hadir',
        ]);

        // LANGKAH 1: Kader input data balita yang berisiko (BB 5.0 kg, TB 68.0 cm)
        $this->actingAs($this->kader);
        $resKader = $this->post(route('kader.pengukuran.store', $this->warga->id), [
            'tipe_kohort' => 'balita',
            'berat_badan' => 5.0,
            'tinggi_badan' => 68.0,
            'posisi_ukur' => 'terlentang',
            'lingkar_kepala' => 43.0,
            'lingkar_lengan' => 11.5,
        ]);
        $resKader->assertSessionHasNoErrors();

        $pemeriksaan = Pemeriksaan::where('warga_id', $this->warga->id)
            ->where('sesi_posyandu_id', $sesiHariIni->id)
            ->firstOrFail();

        $this->assertEquals(
            'terlentang',
            $pemeriksaan->posisi_ukur,
            'Posisi pengukuran dari input Kader harus tersimpan di database'
        );

        $kalkulasiKader = $pemeriksaan->kalkulasiZscore;
        $this->assertNotNull($kalkulasiKader);
        $this->assertContains($kalkulasiKader->ews_status, ['bahaya', 'risiko_tinggi']);
        $this->assertEquals('pending', $kalkulasiKader->alert_status, 'Input berisiko oleh kader harus menghasilkan alert pending');

        // LANGKAH 2: Bidan memvalidasi dan mengoreksi data menjadi normal (BB 9.5 kg, TB 75.0 cm)
        $this->actingAs($this->bidan);
        $resBidan = $this->post(route('bidan.pemeriksaan.store'), [
            'warga_id' => $this->warga->id,
            'sesi_posyandu_id' => $sesiHariIni->id,
            'tipe_kohort' => 'balita',
            'berat_badan' => 9.5,
            'tinggi_badan' => 75.0,
            'posisi_ukur' => 'terlentang',
            'lingkar_kepala' => 46.0,
            'lingkar_lengan' => 14.5,
        ]);
        $resBidan->assertSessionHasNoErrors();

        $this->assertEquals(
            'terlentang',
            $pemeriksaan->fresh()->posisi_ukur,
            'Posisi pengukuran dari input Bidan harus tersimpan di database'
        );

        $kalkulasiAfterBidan = $pemeriksaan->fresh()->kalkulasiZscore;
        $this->assertEquals('normal', $kalkulasiAfterBidan->ews_status);
        // ASSERTION KRUSIAL: Meskipun evaluasi klinis berubah menjadi normal, alert_status TETAP 'pending'
        $this->assertEquals('pending', $kalkulasiAfterBidan->alert_status, 'Alert pending TIDAK BOLEH otomatis resolved saat data dievaluasi menjadi normal');
        $this->assertNull($kalkulasiAfterBidan->resolved_by);
        $this->assertNull($kalkulasiAfterBidan->resolved_at);

        // LANGKAH 3: Bidan membuka form tindak lanjut -> status bertransisi ke in_progress
        $resForm = $this->get(route('bidan.pemeriksaan.tindak-lanjut', $pemeriksaan->id));
        $resForm->assertOk();

        $kalkulasiInProgress = $pemeriksaan->fresh()->kalkulasiZscore;
        $this->assertEquals('in_progress', $kalkulasiInProgress->alert_status, 'Membuka form tindak lanjut harus mengubah alert menjadi in_progress');

        // LANGKAH 4: Bidan menyimpan tindak lanjut resmi (Resolusi sah)
        $resResolve = $this->patch(route('bidan.pemeriksaan.tindak-lanjut.update', $pemeriksaan->id), [
            'tindakan' => 'Pemberian PMT Pemulihan & Konseling Sanitasi',
            'catatan' => 'Pertumbuhan telah dievaluasi, kondisi membaik',
        ]);
        $resResolve->assertRedirect(route('bidan.dashboard'));

        $kalkulasiResolved = $pemeriksaan->fresh()->kalkulasiZscore;
        $this->assertEquals('resolved', $kalkulasiResolved->alert_status, 'Tindak lanjut bidan harus menghasilkan alert resolved');
        $this->assertEquals($this->bidan->id, $kalkulasiResolved->resolved_by, 'resolved_by harus mencatat ID bidan');
        $this->assertNotNull($kalkulasiResolved->resolved_at, 'resolved_at harus tercatat');

        $savedResolvedAt = $kalkulasiResolved->resolved_at->toIso8601String();
        $savedResolvedBy = $kalkulasiResolved->resolved_by;

        // LANGKAH 5: Simulasi evaluasi ulang atau penyimpanan ulang pemeriksaan oleh Bidan
        // Status resolved, resolved_at, dan resolved_by TIDAK BOLEH hilang atau ter-overwrite menjadi pending!
        $evalRecheck = $this->ewsService->evaluate(
            $this->warga,
            -3.0,
            -1.0,
            -2.5,
            6.0,
            $pemeriksaan->id,
            $this->warga->tanggal_lahir,
            $sesiHariIni->tanggal
        );

        $kalkulasiRechecked = $this->ewsService->syncKalkulasiZScore($pemeriksaan, [
            'zscore_bb_u' => -3.0,
            'status_gizi' => 'Kurang',
            'status_stunting' => 'Normal',
            'status_bb_tb' => 'Gizi Kurang',
            'age_in_days' => 365,
        ], $evalRecheck);

        $this->assertEquals('resolved', $kalkulasiRechecked->alert_status, 'Evaluasi ulang tidak boleh mengembalikan alert resolved ke pending');
        $this->assertEquals($savedResolvedAt, $kalkulasiRechecked->resolved_at->toIso8601String(), 'resolved_at harus tetap utuh');
        $this->assertEquals($savedResolvedBy, $kalkulasiRechecked->resolved_by, 'resolved_by harus tetap utuh');
    }
}
