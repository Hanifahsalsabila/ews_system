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

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('guest')->prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AdminLoginController::class, 'create'])->name('login');
    Route::post('login', [AdminLoginController::class, 'store'])->name('login.store');
});

Route::middleware(['auth', 'role:Super_Admin|Admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Admin Profile
    Route::get('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'edit'])->name('profile.edit');
    
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    Route::resource('regions', RegionController::class);
    
    Route::post('wargas/import', [WargaController::class, 'import'])->name('wargas.import');
    Route::get('wargas/qr/{nik}', [WargaController::class, 'qrCode'])->name('wargas.qr');
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

Route::middleware(['auth', 'role:Bidan_Desa|Puskesmas'])->prefix('bidan')->name('bidan.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Bidan\DashboardController::class, 'index'])->name('dashboard');
    
    // Antenatal Care & Pemeriksaan
    Route::get('pemeriksaan/ibu-hamil', [\App\Http\Controllers\Bidan\PemeriksaanController::class, 'ibuHamil'])->name('pemeriksaan.ibu-hamil');
    Route::get('pemeriksaan/balita', [\App\Http\Controllers\Bidan\PemeriksaanController::class, 'balita'])->name('pemeriksaan.balita');
    Route::resource('pemeriksaan', \App\Http\Controllers\Bidan\PemeriksaanController::class);
    
    // KMS & Stunting
    Route::get('kms', [\App\Http\Controllers\Bidan\KmsController::class, 'index'])->name('kms.index');
    
    // Inventaris / Logistik
    Route::get('logistik', [\App\Http\Controllers\Bidan\LogistikController::class, 'index'])->name('logistik.index');
    
    // Pelaporan
    Route::get('pelaporan', [\App\Http\Controllers\Bidan\PelaporanController::class, 'index'])->name('pelaporan.index');
    Route::post('pelaporan/export', [\App\Http\Controllers\Bidan\PelaporanController::class, 'export'])->name('pelaporan.export');
    
    // Notifikasi WhatsApp
    Route::get('notifikasi', [\App\Http\Controllers\Bidan\NotifikasiController::class, 'index'])->name('notifikasi.index');
    
    // Profile
    Route::get('profile', [\App\Http\Controllers\Bidan\ProfileController::class, 'edit'])->name('profile.edit');
});

Route::middleware(['auth', 'role:Kader_Posyandu'])->prefix('kader')->name('kader.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Kader\KaderDashboardController::class, 'index'])->name('dashboard');
    Route::post('/sesi', [\App\Http\Controllers\Kader\KaderDashboardController::class, 'storeSesi'])->name('sesi.store');
    Route::patch('/sesi/{sesi}/toggle', [\App\Http\Controllers\Kader\KaderDashboardController::class, 'toggleSesi'])->name('sesi.toggle');
    
    // Meja 1: Pendaftaran / Absensi
    Route::get('pendaftaran', [\App\Http\Controllers\Kader\PendaftaranController::class, 'index'])->name('pendaftaran.index');
    Route::post('pendaftaran/scan', [\App\Http\Controllers\Kader\PendaftaranController::class, 'scan'])->name('pendaftaran.scan');
    Route::post('pendaftaran/store', [\App\Http\Controllers\Kader\PendaftaranController::class, 'store'])->name('pendaftaran.store');
    
    // Manajemen Warga
    Route::resource('warga', \App\Http\Controllers\Kader\WargaController::class)->except(['show']);
    
    // Meja 2 & 3: Pengukuran & Penimbangan
    Route::get('pengukuran', [\App\Http\Controllers\Kader\PengukuranController::class, 'index'])->name('pengukuran.index');
    Route::get('pengukuran/{warga}/create', [\App\Http\Controllers\Kader\PengukuranController::class, 'create'])->name('pengukuran.create');
    Route::post('pengukuran/{warga}', [\App\Http\Controllers\Kader\PengukuranController::class, 'store'])->name('pengukuran.store');
    
    // Profile
    Route::get('profile', [\App\Http\Controllers\Kader\ProfileController::class, 'edit'])->name('profile.edit');
});

require __DIR__.'/auth.php';
