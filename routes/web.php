<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Admin\RegionController;
use App\Http\Controllers\Admin\WargaController;
use App\Http\Controllers\Admin\SesiPosyanduController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Public Warga Verification Route
Route::get('/v/{token}', [\App\Http\Controllers\Admin\WargaController::class, 'verify'])->name('warga.verify');

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
    Route::get('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'edit'])->name('profile.edit');
    
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    Route::patch('users/{user}/toggle-status', [\App\Http\Controllers\Admin\UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::resource('regions', RegionController::class);
    
    Route::post('wargas/import', [WargaController::class, 'import'])->name('wargas.import');
    Route::get('wargas/qr/{nik}', [WargaController::class, 'qrCode'])->name('wargas.qr');
    Route::post('wargas/{warga}/generate-account', [WargaController::class, 'generateAccount'])->name('wargas.generate-account');
    Route::resource('wargas', WargaController::class);

    Route::resource('sesi-posyandus', SesiPosyanduController::class);
    Route::patch('sesi-posyandus/{sesi}/toggle', [SesiPosyanduController::class, 'toggleStatus'])->name('sesi-posyandus.toggle');
    Route::get('sesi-posyandus/{sesi}/operasional', [SesiPosyanduController::class, 'operasional'])->name('sesi-posyandus.operasional');
    Route::post('sesi-posyandus/{sesi}/operasional', [SesiPosyanduController::class, 'storeOperasional'])->name('sesi-posyandus.store-operasional');

    Route::resource('vaksins', \App\Http\Controllers\Admin\VaksinController::class);
    Route::resource('inventaris', \App\Http\Controllers\Admin\InventarisController::class);
    Route::resource('audits', \App\Http\Controllers\Admin\AuditController::class);
    
    Route::get('pelaporan', [\App\Http\Controllers\Admin\PelaporanController::class, 'index'])->name('pelaporan.index');
    Route::post('pelaporan/export', [\App\Http\Controllers\Admin\PelaporanController::class, 'export'])->name('pelaporan.export');
});

Route::middleware(['auth', 'role:Puskesmas'])->prefix('bidan')->name('bidan.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Bidan\DashboardController::class, 'index'])->name('dashboard');
    
    // Antenatal Care & Pemeriksaan
    Route::get('pemeriksaan/ibu-hamil', [\App\Http\Controllers\Bidan\PemeriksaanController::class, 'ibuHamil'])->name('pemeriksaan.ibu-hamil');
    Route::get('pemeriksaan/balita', [\App\Http\Controllers\Bidan\PemeriksaanController::class, 'balita'])->name('pemeriksaan.balita');
    Route::get('pemeriksaan/lansia', [\App\Http\Controllers\Bidan\PemeriksaanController::class, 'lansia'])->name('pemeriksaan.lansia');
    Route::resource('pemeriksaan', \App\Http\Controllers\Bidan\PemeriksaanController::class);
    
    // KMS & Stunting
    Route::get('kms', [\App\Http\Controllers\Bidan\KmsController::class, 'index'])->name('kms.index');
    
    // Inventaris / Logistik
    Route::get('logistik', [\App\Http\Controllers\Bidan\LogistikController::class, 'index'])->name('logistik.index');
    Route::post('logistik/vaksin', [\App\Http\Controllers\Bidan\LogistikController::class, 'storeVaksin'])->name('logistik.vaksin.store');
    Route::post('logistik/inventaris', [\App\Http\Controllers\Bidan\LogistikController::class, 'storeInventaris'])->name('logistik.inventaris.store');
    
    // Pelaporan
    Route::get('pelaporan', [\App\Http\Controllers\Bidan\PelaporanController::class, 'index'])->name('pelaporan.index');
    Route::post('pelaporan/export', [\App\Http\Controllers\Bidan\PelaporanController::class, 'export'])->name('pelaporan.export');
    
    
    // Profile
    Route::get('profile', [\App\Http\Controllers\Bidan\ProfileController::class, 'edit'])->name('profile.edit');

    // Kunjungan Rumah & EWS
    Route::get('kunjungan', [\App\Http\Controllers\Bidan\KunjunganRumahController::class, 'index'])->name('kunjungan.index');
    Route::post('kunjungan/{kunjungan}/claim', [\App\Http\Controllers\Bidan\KunjunganRumahController::class, 'claim'])->name('kunjungan.claim');
    Route::get('kunjungan/{kunjungan}/edit', [\App\Http\Controllers\Bidan\KunjunganRumahController::class, 'edit'])->name('kunjungan.edit');
    Route::put('kunjungan/{kunjungan}', [\App\Http\Controllers\Bidan\KunjunganRumahController::class, 'update'])->name('kunjungan.update');

    // Penyuluhan (CRUD)
    Route::resource('penyuluhan', \App\Http\Controllers\Bidan\PenyuluhanController::class)->except(['show']);
    Route::patch('penyuluhan/{penyuluhan}/toggle-publish', [\App\Http\Controllers\Bidan\PenyuluhanController::class, 'togglePublish'])->name('penyuluhan.toggle-publish');
});

Route::middleware(['auth', 'role:Kader_Posyandu'])->prefix('kader')->name('kader.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Kader\KaderDashboardController::class, 'index'])->name('dashboard');
    Route::post('/sesi', [\App\Http\Controllers\Kader\KaderDashboardController::class, 'storeSesi'])->name('sesi.store');
    Route::patch('/sesi/{sesi}/toggle', [\App\Http\Controllers\Kader\KaderDashboardController::class, 'toggleSesi'])->name('sesi.toggle');
    
    // Meja 1: Pendaftaran / Absensi
    Route::get('pendaftaran', [\App\Http\Controllers\Kader\PendaftaranController::class, 'index'])->name('pendaftaran.index');
    Route::post('pendaftaran/scan', [\App\Http\Controllers\Kader\PendaftaranController::class, 'scan'])->name('pendaftaran.scan');
    Route::post('pendaftaran/store', [\App\Http\Controllers\Kader\PendaftaranController::class, 'store'])->name('pendaftaran.store');
    
    // Buku Register Warga
    Route::post('warga/{warga}/generate-account', [\App\Http\Controllers\Kader\WargaController::class, 'generateAccount'])->name('warga.generate-account');
    Route::resource('warga', \App\Http\Controllers\Kader\WargaController::class);
    Route::get('warga/qr/{nik}', [\App\Http\Controllers\Admin\WargaController::class, 'qrCode'])->name('warga.qr');
    
    // Meja 2 & 3: Pengukuran & Penimbangan
    Route::get('pengukuran', [\App\Http\Controllers\Kader\PengukuranController::class, 'index'])->name('pengukuran.index');
    Route::get('pengukuran/{warga}/create', [\App\Http\Controllers\Kader\PengukuranController::class, 'create'])->name('pengukuran.create');
    Route::post('pengukuran/{warga}', [\App\Http\Controllers\Kader\PengukuranController::class, 'store'])->name('pengukuran.store');
    
    // Profile
    Route::get('profile', [\App\Http\Controllers\Kader\ProfileController::class, 'edit'])->name('profile.edit');

    // Penyuluhan (Read-Only)
    Route::get('penyuluhan', [\App\Http\Controllers\Kader\PenyuluhanController::class, 'index'])->name('penyuluhan.index');
    Route::get('penyuluhan/{penyuluhan}', [\App\Http\Controllers\Kader\PenyuluhanController::class, 'show'])->name('penyuluhan.show');
});

// ── Portal Warga / Orang Tua ──
Route::middleware([
    'auth', 
    'role:orang_tua',
    'check_warga_data'
])->prefix('portal')->name('warga.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Warga\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/riwayat', [\App\Http\Controllers\Warga\RiwayatController::class, 'index'])->name('riwayat');
    Route::get('/badges', [\App\Http\Controllers\Warga\BadgeController::class, 'index'])->name('badges');
    Route::get('/kms/{warga}', [\App\Http\Controllers\Warga\DashboardController::class, 'kms'])->name('kms');
    Route::get('/profile', [\App\Http\Controllers\Warga\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/password', [\App\Http\Controllers\Warga\ProfileController::class, 'updatePassword'])->name('profile.password');

    // Penyuluhan / Edukasi (Read-Only)
    Route::get('/edukasi', [\App\Http\Controllers\Warga\PenyuluhanController::class, 'index'])->name('edukasi.index');
    Route::get('/edukasi/{penyuluhan}', [\App\Http\Controllers\Warga\PenyuluhanController::class, 'show'])->name('edukasi.show');
});

require __DIR__.'/auth.php';

