<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Warga;
use App\Models\Balita;
use App\Models\SesiPosyandu;
use App\Models\Pemeriksaan;
use App\Models\KalkulasiZscore;
use App\Models\Region;
use Carbon\Carbon;

class KmsAgeDisplayTest extends TestCase
{
    use RefreshDatabase;

    public function test_age_displayed_correctly_on_kms_page()
    {
        // Set fixed current time just in case to verify it does not use now()
        Carbon::setTestNow(Carbon::parse('2026-11-01'));

        $user = User::factory()->create();
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Puskesmas']);
        $user->assignRole('Puskesmas');

        $region = Region::create([
            'name' => 'Region A',
            'type' => 'dusun'
        ]);

        $warga = Warga::create([
            'nik' => '1234567890123456',
            'nama' => 'Arlienna',
            'tanggal_lahir' => '2026-06-08',
            'jenis_kelamin' => 'P',
            'alamat_lengkap' => 'Alamat A',
            'region_id' => $region->id
        ]);

        $balita = Balita::create([
            'warga_id' => $warga->id,
            'nama_ayah' => 'Ayah',
            'nama_ibu' => 'Ibu'
        ]);

        $sesi = SesiPosyandu::create([
            'nama_sesi' => 'Sesi A',
            'tanggal' => '2026-10-05',
            'status' => 'aktif'
        ]);

        $sesi2 = SesiPosyandu::create([
            'nama_sesi' => 'Sesi B',
            'tanggal' => '2026-10-08',
            'status' => 'aktif'
        ]);

        // 05/10/2026 -> 3 Bulan 27 Hari
        $pem1 = Pemeriksaan::create([
            'warga_id' => $warga->id,
            'sesi_posyandu_id' => $sesi->id,
            'berat_badan' => 5.5,
            'tipe_kohort' => 'balita',
            'is_validated' => true
        ]);
        $pem1->created_at = '2026-10-05 08:00:00';
        $pem1->save(['timestamps' => false]);
        
        KalkulasiZscore::create([
            'pemeriksaan_id' => $pem1->id,
            'zscore_bb_u' => 0.0
        ]);

        // 08/10/2026 -> 4 Bulan
        $pem2 = Pemeriksaan::create([
            'warga_id' => $warga->id,
            'sesi_posyandu_id' => $sesi2->id,
            'berat_badan' => 6.0,
            'tipe_kohort' => 'balita',
            'is_validated' => true
        ]);
        $pem2->created_at = '2026-10-08 08:00:00';
        $pem2->save(['timestamps' => false]);
        
        KalkulasiZscore::create([
            'pemeriksaan_id' => $pem2->id,
            'zscore_bb_u' => 0.5
        ]);

        $response = $this->actingAs($user)->get(route('bidan.kms.index', ['balita_id' => $warga->id]));

        $response->assertStatus(200);
        $response->assertSee('3 Bulan 27 Hari');
        $response->assertSee('4 Bulan');

        Carbon::setTestNow();
    }
}
