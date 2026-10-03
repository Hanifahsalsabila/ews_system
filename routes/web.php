<?php

use App\Http\Controllers\Admin\AuditController;
use App\Http\Controllers\Admin\InventarisController;
use App\Http\Controllers\Admin\PelaporanController;
use App\Http\Controllers\Admin\RegionController;
use App\Http\Controllers\Admin\SesiPosyanduController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VaksinController;
use App\Http\Controllers\Admin\WargaController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Bidan\KmsController;
use App\Http\Controllers\Bidan\KunjunganRumahController;
use App\Http\Controllers\Bidan\LogistikController;
use App\Http\Controllers\Bidan\PemeriksaanController;
use App\Http\Controllers\Bidan\PenyuluhanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Kader\KaderDashboardController;
use App\Http\Controllers\Kader\PendaftaranController;
use App\Http\Controllers\Kader\PengukuranController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Warga\BadgeController;
use App\Http\Controllers\Warga\RiwayatController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Public Warga Verification Route
Route::get('/v/{token}', [WargaController::class, 'verify'])->name('warga.verify');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('guest')->prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AdminLoginController::class, 'create'])->name('login');
    Route::post('login', [AdminLoginController::class, 'store'])->name('login.store');
});

Route::middleware(['auth', 'role:Super_Admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Admin Profile
    Route::get('/profile', [App\Http\Controllers\Admin\ProfileController::class, 'edit'])->name('profile.edit');

    Route::resource('users', UserController::class);
    Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::resource('regions', RegionController::class);

    Route::post('wargas/import', [WargaController::class, 'import'])->name('wargas.import');
    Route::get('wargas/qr/{nik}', [WargaController::class, 'qrCode'])->name('wargas.qr');
    Route::post('wargas/{warga}/generate-account', [WargaController::class, 'generateAccount'])->name('wargas.generate-account');
    Route::resource('wargas', WargaController::class);

    Route::resource('sesi-posyandus', SesiPosyanduController::class);
    Route::patch('sesi-posyandus/{sesi}/toggle', [SesiPosyanduController::class, 'toggleStatus'])->name('sesi-posyandus.toggle');
    Route::get('sesi-posyandus/{sesi}/operasional', [SesiPosyanduController::class, 'operasional'])->name('sesi-posyandus.operasional');
    Route::post('sesi-posyandus/{sesi}/operasional', [SesiPosyanduController::class, 'storeOperasional'])->name('sesi-posyandus.store-operasional');

    Route::resource('vaksins', VaksinController::class);
    Route::resource('inventaris', InventarisController::class);
    Route::resource('audits', AuditController::class);

    Route::get('pelaporan', [PelaporanController::class, 'index'])->name('pelaporan.index');
    Route::post('pelaporan/export', [PelaporanController::class, 'export'])->name('pelaporan.export');
});

Route::middleware(['auth', 'role:Puskesmas'])->prefix('bidan')->name('bidan.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Bidan\DashboardController::class, 'index'])->name('dashboard');

    // Antenatal Care & Pemeriksaan
    Route::get('pemeriksaan/ibu-hamil', [PemeriksaanController::class, 'ibuHamil'])->name('pemeriksaan.ibu-hamil');
    Route::get('pemeriksaan/balita', [PemeriksaanController::class, 'balita'])->name('pemeriksaan.balita');
    Route::get('pemeriksaan/lansia', [PemeriksaanController::class, 'lansia'])->name('pemeriksaan.lansia');
    Route::get('pemeriksaan/tindak-lanjut/{pemeriksaan}', [PemeriksaanController::class, 'editTindakLanjut'])->name('pemeriksaan.tindak-lanjut');
    Route::patch('pemeriksaan/tindak-lanjut/{pemeriksaan}', [PemeriksaanController::class, 'updateTindakLanjut'])->name('pemeriksaan.tindak-lanjut.update');
    Route::resource('pemeriksaan', PemeriksaanController::class);

    // KMS & Stunting
    Route::get('kms', [KmsController::class, 'index'])->name('kms.index');

    // Inventaris / Logistik
    Route::get('logistik', [LogistikController::class, 'index'])->name('logistik.index');
    Route::post('logistik/vaksin', [LogistikController::class, 'storeVaksin'])->name('logistik.vaksin.store');
    Route::post('logistik/inventaris', [LogistikController::class, 'storeInventaris'])->name('logistik.inventaris.store');

    // Pelaporan
    Route::get('pelaporan', [App\Http\Controllers\Bidan\PelaporanController::class, 'index'])->name('pelaporan.index');
    Route::post('pelaporan/export', [App\Http\Controllers\Bidan\PelaporanController::class, 'export'])->name('pelaporan.export');

    // Profile
    Route::get('profile', [App\Http\Controllers\Bidan\ProfileController::class, 'edit'])->name('profile.edit');

    // Kunjungan Rumah & EWS
    Route::get('kunjungan', [KunjunganRumahController::class, 'index'])->name('kunjungan.index');
    Route::post('kunjungan/{kunjungan}/claim', [KunjunganRumahController::class, 'claim'])->name('kunjungan.claim');
    Route::get('kunjungan/{kunjungan}/edit', [KunjunganRumahController::class, 'edit'])->name('kunjungan.edit');
    Route::put('kunjungan/{kunjungan}', [KunjunganRumahController::class, 'update'])->name('kunjungan.update');

    // Penyuluhan (CRUD)
    Route::resource('penyuluhan', PenyuluhanController::class)->except(['show']);
    Route::patch('penyuluhan/{penyuluhan}/toggle-publish', [PenyuluhanController::class, 'togglePublish'])->name('penyuluhan.toggle-publish');
});

Route::middleware(['auth', 'role:Kader_Posyandu'])->prefix('kader')->name('kader.')->group(function () {
    Route::get('/dashboard', [KaderDashboardController::class, 'index'])->name('dashboard');
    Route::post('/sesi', [KaderDashboardController::class, 'storeSesi'])->name('sesi.store');
    Route::patch('/sesi/{sesi}/toggle', [KaderDashboardController::class, 'toggleSesi'])->name('sesi.toggle');

    // Meja 1: Pendaftaran / Absensi
    Route::get('pendaftaran', [PendaftaranController::class, 'index'])->name('pendaftaran.index');
    Route::post('pendaftaran/scan', [PendaftaranController::class, 'scan'])->name('pendaftaran.scan');
    Route::post('pendaftaran/store', [PendaftaranController::class, 'store'])->name('pendaftaran.store');

    // Buku Register Warga
    Route::post('warga/{warga}/generate-account', [App\Http\Controllers\Kader\WargaController::class, 'generateAccount'])->name('warga.generate-account');
    Route::resource('warga', App\Http\Controllers\Kader\WargaController::class);
    Route::get('warga/qr/{nik}', [WargaController::class, 'qrCode'])->name('warga.qr');

    // Meja 2 & 3: Pengukuran & Penimbangan
    Route::get('pengukuran', [PengukuranController::class, 'index'])->name('pengukuran.index');
    Route::get('pengukuran/{warga}/create', [PengukuranController::class, 'create'])->name('pengukuran.create');
    Route::post('pengukuran/{warga}', [PengukuranController::class, 'store'])->name('pengukuran.store');

    // Profile
    Route::get('profile', [App\Http\Controllers\Kader\ProfileController::class, 'edit'])->name('profile.edit');

    // Penyuluhan (Read-Only)
    Route::get('penyuluhan', [App\Http\Controllers\Kader\PenyuluhanController::class, 'index'])->name('penyuluhan.index');
    Route::get('penyuluhan/{penyuluhan}', [App\Http\Controllers\Kader\PenyuluhanController::class, 'show'])->name('penyuluhan.show');
});

// ── Portal Warga / Orang Tua ──
Route::middleware([
    'auth',
    'role:orang_tua',
    'check_warga_data',
])->prefix('portal')->name('warga.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Warga\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat');
    Route::get('/badges', [BadgeController::class, 'index'])->name('badges');
    Route::get('/kms/{warga}', [App\Http\Controllers\Warga\DashboardController::class, 'kms'])->name('kms');
    Route::get('/profile', [App\Http\Controllers\Warga\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/password', [App\Http\Controllers\Warga\ProfileController::class, 'updatePassword'])->name('profile.password');

    // Penyuluhan / Edukasi (Read-Only)
    Route::get('/edukasi', [App\Http\Controllers\Warga\PenyuluhanController::class, 'index'])->name('edukasi.index');
    Route::get('/edukasi/{penyuluhan}', [App\Http\Controllers\Warga\PenyuluhanController::class, 'show'])->name('edukasi.show');
});

require __DIR__.'/auth.php';
