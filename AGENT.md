# AGENT.md — E-POSYANDU MASTER RULES

> Dokumen ini WAJIB dibaca dan dipatuhi oleh SEMUA AI model (termasuk OpenCode, Claude, ChatGPT, GitHub Copilot, Cursor, dll.) dalam setiap pengembangan dan pemeliharaan aplikasi **E-Posyandu (posyandu-UAS)**.

---

## 1. PROJECT OVERVIEW

| Atribut | Nilai |
|---------|-------|
| **Framework** | Laravel 13.x (PHP 8.3+) |
| **Frontend** | Blade + Tailwind CSS 3.x + Alpine.js + Chart.js |
| **Database** | MySQL (dev), SQLite :memory: (testing) |
| **Auth** | Spatie Laravel Permission + Laravel Breeze |
| **Testing** | PHPUnit 12.x |
| **Build** | Vite 8.x |
| **Export** | Maatwebsite Excel (Laravel Excel) |
| **Audit** | owen-it/laravel-auditing |
| **QR Code** | simplesoftwareio/simple-qrcode |

---

## 2. WORKFLOW Wajib (SETIAP KALI NGODING)

```
┌─────────────────────────────────────────────────────┐
│ 1. EDIT CODE di localhost                            │
│    ├─ Pastikan ikuti pola kode yang sudah ada         │
│    └─ JANGAN tambah komentar (~kecuali memang perlu)  │
└──────────────────────┬──────────────────────────────┘
                       ▼
┌─────────────────────────────────────────────────────┐
│ 2. TEST di localhost                                 │
│    ├─ composer run test  (PHPUnit)                   │
│    ├─ npm run build    (Vite, cek error frontend)    │
│    └─ Jika GAGAL → fix kode → ulang step 2           │
└──────────────────────┬──────────────────────────────┘
                       ▼
┌─────────────────────────────────────────────────────┐
│ 3. PUSH & COMMIT ke GitHub                           │
│    ├─ git add .                                      │
│    ├─ git commit -m "feat/fix: pesan jelas"          │
│    └─ git push                                       │
└──────────────────────┬──────────────────────────────┘
                       ▼
┌─────────────────────────────────────────────────────┐
│ 4. AUTO TEST di GitHub (CI/CD)                       │
│    ├─ Pantau hasil GitHub Actions / Checks            │
│    ├─ Jika LOLOS ✅ → SELESAI                         │
│    └─ Jika GAGAL ❌ → lanjut ke step 5               │
└──────────────────────┬──────────────────────────────┘
                       ▼
┌─────────────────────────────────────────────────────┐
│ 5. ANALISA HASIL TEST GAGAL                          │
│    ├─ Baca log error dari GitHub Actions              │
│    ├─ Identifikasi root cause                         │
│    ├─ Fix kode                                       │
│    └─ Kembali ke step 2 (test local dulu)            │
└─────────────────────────────────────────────────────┘
```

**WAJIB**: Jangan pernah push langsung tanpa test di localhost terlebih dahulu.

---

## 3. ANALISIS ALIGNMENT SISTEM

### 3.1 User Roles & Access Matrix

| Fitur | Super_Admin | Admin | Bidan_Desa | Puskesmas | Kader_Posyandu | orang_tua |
|-------|:-----------:|:-----:|:----------:|:---------:|:--------------:|:---------:|
| Dashboard | `/admin` | `/admin` | `/bidan` | `/bidan` | `/kader` | `/portal` |
| Manajemen User | FULL | FULL | - | - | - | - |
| Manajemen Wilayah | FULL | FULL | - | - | - | - |
| Data Warga | FULL+Import+QR | FULL+Import+QR | - | - | FULL+Gen.Acc | - |
| Sesi Posyandu | FULL+Toggl+Ops | FULL+Toggl+Ops | - | - | Create+Toggl | - |
| Master Vaksin | FULL | FULL | - | - | - | - |
| Master Inventaris | FULL | FULL | - | - | - | - |
| Logistik | - | - | VIEW+Stok | VIEW+Stok | - | - |
| Pemeriksaan | - | - | FULL+Validasi | FULL+Validasi | Ukur saja | Lihat sendiri |
| KMS/Stunting | - | - | FULL | FULL | - | Lihat sendiri |
| Kunjungan Rumah | - | - | Claim+Edit | Claim+Edit | Auto-create (EWS) | - |
| Penyuluhan | - | - | FULL CRUD | FULL CRUD | VIEW | VIEW |
| Pelaporan/Export | Export SIP | Export SIP | Export+Stats | Export+Stats | - | - |
| Audit Trail | VIEW | VIEW | - | - | - | - |
| Notifikasi WA | - | - | VIEW | VIEW | - | - |
| Badges | - | - | - | - | - | VIEW |

### 3.2 Arsitektur Routing

```
web.php
├── Auth routes         → /login, /register, /forgot-password, dll.
├── /                   → welcome (public)
├── /dashboard          → redirect berdasarkan role
├── /v/{token}          → verifikasi QR publik
├── /profile            → edit profil umum (auth)
│
├── /admin/*            → middleware: auth, role:Super_Admin|Admin
├── /bidan/*            → middleware: auth, role:Bidan_Desa|Puskesmas
├── /kader/*            → middleware: auth, role:Kader_Posyandu
└── /portal/*           → middleware: auth, role:orang_tua, check_warga_data
```

### 3.3 Layout Structure

| Layout | Role | Warna Tema | Navigasi | Target Device |
|--------|------|-----------|----------|---------------|
| `layouts/admin.blade.php` | Admin | Emerald (dark sidebar) | Sidebar fixed 72 | Desktop |
| `layouts/bidan.blade.php` | Bidan | Emerald (light sidebar) | Sidebar fixed 72 | Desktop |
| `layouts/kader.blade.php` | Kader | Teal/Green (gradient) | Sidebar fixed 72 | Desktop |
| `layouts/warga.blade.php` | Warga | Emerald (gradient) | Bottom nav | Mobile-first |
| `layouts/app.blade.php` | General | Standard Breeze | Top nav | All |
| `layouts/guest.blade.php` | Guest | Centered simple | None | All |

### 3.4 Database Relasi Utama

```
users (1) ──< (0..1) warga (1) ──< (0..1) balita
                                ├──< (0..1) ibu_hamil
                                ├──< (0..1) lansia
                                ├──< (0..*) absensi
                                ├──< (0..*) pemeriksaan
                                ├──< (0..*) kunjungan_rumahs
                                │
regions (1) ──< (0..*) warga
regions (1) ──< (0..*) regions (parent_id = self-referencing hierarki dusun/rw/rt)

sesi_posyandu (1) ──< (0..*) absensi
sesi_posyandu (1) ──< (0..*) pemeriksaan

pemeriksaan (1) ──< (0..1) kalkulasi_zscore
pemeriksaan (1) ──< (0..*) tindakan_medis

users (1) ──< (0..*) penyuluhans (penulis_id)
users (1) ──< (0..*) kunjungan_rumahs (bidan_id)
```

### 3.5 Frontend-Backend Sync

| Kategori Backend | Controller | View | Route Name | Status |
|-----------------|-----------|------|-----------|--------|
| Dashboard Admin | `AdminDashboardController` | `admin/dashboard/index` | admin.dashboard | ✅ |
| Users CRUD | `Admin\UserController` | `admin/users/index` | admin.users.* | ✅ |
| Regions CRUD | `Admin\RegionController` | `admin/regions/index` | admin.regions.* | ✅ |
| Warga CRUD | `Admin\WargaController` | `admin/warga/index` | admin.wargas.* | ✅ |
| Sesi Posyandu | `Admin\SesiPosyanduController` | `admin/sesi/index` | admin.sesi-posyandus.* | ✅ |
| Operasional | `Admin\SesiPosyanduController` | `admin/sesi/operasional` | admin.sesi-posyandus.operasional | ✅ |
| Vaksin | `Admin\VaksinController` | `admin/vaksin/index` | admin.vaksins.* | ✅ |
| Inventaris | `Admin\InventarisController` | `admin/inventaris/index` | admin.inventaris.* | ✅ |
| Audit | `Admin\AuditController` | `admin/audits/index` | admin.audits.* | ✅ |
| Pelaporan | `Admin\PelaporanController` | `admin/laporan/index` | admin.pelaporan.* | ✅ |
| Dashboard Bidan | `Bidan\DashboardController` | `bidan/dashboard` | bidan.dashboard | ✅ |
| Pemeriksaan | `Bidan\PemeriksaanController` | `bidan/pemeriksaan/*` | bidan.pemeriksaan.* | ✅ |
| KMS | `Bidan\KmsController` | `bidan/kms/index` | bidan.kms.index | ✅ |
| Logistik | `Bidan\LogistikController` | `bidan/logistik/index` | bidan.logistik.* | ✅ |
| Kunjungan | `Bidan\KunjunganRumahController` | `bidan/kunjungan/*` | bidan.kunjungan.* | ✅ |
| Penyuluhan | `Bidan\PenyuluhanController` | `bidan/penyuluhan/*` | bidan.penyuluhan.* | ✅ |
| Notifikasi | `Bidan\NotifikasiController` | `bidan/notifikasi/index` | bidan.notifikasi.index | ✅ |
| Dashboard Kader | `Kader\KaderDashboardController` | `kader/dashboard/index` | kader.dashboard | ✅ |
| Pendaftaran | `Kader\PendaftaranController` | `kader/pendaftaran/scan` | kader.pendaftaran.* | ✅ |
| Warga Kader | `Kader\WargaController` | `kader/warga/index` | kader.warga.* | ✅ |
| Pengukuran | `Kader\PengukuranController` | `kader/pengukuran/*` | kader.pengukuran.* | ✅ |
| Dashboard Warga | `Warga\DashboardController` | `warga/dashboard` | warga.dashboard | ✅ |
| Riwayat | `Warga\RiwayatController` | `warga/kms` | warga.riwayat | ✅ |
| Badges | `Warga\BadgeController` | `warga/badges/index` | warga.badges | ✅ |
| Edukasi | `Warga\PenyuluhanController` | `warga/penyuluhan/*` | warga.edukasi.* | ✅ |

---

## 4. STATUS PERBAIKAN BUG

### ✅ SUDAH DIPERBAIKI

| # | Bug | Perbaikan |
|---|-----|-----------|
| 1 | Role middleware `Super_Admin|Admin` → `Super_Admin` saja (sengaja) | web.php line 34 |
| 2 | Role `Puskesmas` tidak di-seed | Ditambahkan ke `RoleSeeder` |
| 3 | Notifikasi WhatsApp cuma display (route & controller dihapus) | File & route dihapus |
| 4 | Z-score tidak dihitung Kader | Ditambahkan di `PengukuranController@store` lines 91-115 |
| 5 | Migration kosong `085933` | Diisi dengan add column `username` (+ guard `hasColumn`) |
| 6 | Badge tidak pernah di-award | Ditambahkan logic hitung streak absensi per bulan |
| 7 | Import region name match fragile | Improved: `TRIM(LOWER())`, normalisasi spasi, tampilkan region yg tersedia di error |
| 8 | Email nullable vs required | `RegisteredUserController` validasi email jadi `nullable` |

### ⚠️ INTENTIONAL (sengaja)

| Keputusan | Alasan |
|-----------|--------|
| Role `Admin` dihapus | Diganti `Super_Admin` saja |
| Role `Bidan_Desa` dihapus | Diganti `Puskesmas` saja |
| Route `bidan` → `role:Puskesmas` | Konsolidasi role bidan |
| Notifikasi WA dihapus | Fitur tidak pernah diimplementasi |

---

## 5. ATURAN KODE (CODING STANDARDS)

### 5.1 Controller Pattern
```php
class NamaController extends Controller
{
    public function index() {}    // GET daftar
    public function store() {}    // POST create
    public function show($id) {}  // GET detail
    public function update($id) {} // PUT/PATCH
    public function destroy($id) {} // DELETE
}
```

### 5.2 Blade View Pattern
- Layout: `@extends('layouts.<role>')` atau `@extends('layouts.app')`
- Section: `@section('content')` ... `@endsection`
- Component: `@include('components.nama')`
- UI: Tailwind classes, inline SVG icons (Heroicons style)
- Interactivity: Alpine.js (`x-data`, `x-on:click`, dll.)

### 5.3 Route Pattern
```php
Route::middleware(['auth', 'role:Super_Admin|Admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', UserController::class);
});
```

### 5.4 Migration Pattern
- Satu migration per perubahan
- Gunakan `Schema::table()` untuk modify existing table
- `$table->foreignId('...')->constrained()->cascadeOnDelete()`

### 5.5 Model Pattern
- Gunakan `$fillable` (bukan `$guarded`)
- Relasi: `belongsTo()`, `hasMany()`, `hasOne()`
- SoftDeletes untuk data master (warga, balita, dll.)

### 5.6 UI/UX CONSISTENCY
- **Warna**: Emerald (`#059669`) sebagai primary di semua role
- **Font**: Plus Jakarta Sans (admin/warga), Inter (bidan/kader)
- **Border radius**: `rounded-2xl` untuk card, `rounded-xl` untuk button
- **Shadow**: `shadow-sm` / `shadow-md`
- **Toast**: Posisi bottom-right (admin), top-center (warga)
- **Loading**: `x-cloak` untuk Alpine.js FOUC prevention

---

## 6. TESTING RULES

### 6.1 Local Testing Commands
```bash
# PHPUnit (wajib)
composer run test

# Atau manual:
php artisan config:clear
php artisan test

# Build Vite (cek error frontend)
npm run build
```

### 6.2 PHPUnit Config (phpunit.xml)
- DB: `sqlite :memory:` — test pakai in-memory database
- Tidak perlu `.env.testing`
- Cache: `array`, Queue: `sync`, Session: `array`

### 6.3 Test Coverage Wajib
Setiap kali nambah fitur baru, WAJIB buat test minimal:
- **Feature Test**: satu test sukses, satu test gagal (validasi/otorisasi)
- **Unit Test**: untuk model/service logic baru

---

## 7. GIT & GITHUB RULES

### 7.1 Commit Message Format
```
<type>: <deskripsi singkat>

Types: feat | fix | refactor | test | docs | chore | style | db
```

Contoh:
```
feat: add export filter by date range for bidan laporan
fix: validate NIK length before QR code generation
db: add nullable email to users table
```

### 7.2 Branch Naming
```
feat/<fitur>
fix/<bug>
refactor/<bagian>
```

### 7.3 Sebelum Commit
```bash
git status                 # cek apa aja yang berubah
git diff                   # review kode
composer run test          # test dulu
npm run build              # build frontend
```

### 7.4 Setelah Push
- Pantau GitHub Actions/Checks
- Jika test gagal → baca log, fix, test ulang lokal, push lagi

---

## 8. ROLE PENGEMBANGAN AI

| Situasi | Yang Harus Dilakukan AI |
|---------|------------------------|
| User minta fitur baru | Cek dulu route/view/controller yang mirip, ikuti pola |
| User lapor bug | Cek di route, controller, view, database — trace end-to-end |
| User minta refactor | Jangan ubah logic, cuma struktur/penamaan |
| User minta test | Buat PHPUnit test sesuai pola existing |
| User tanya desain | Rujuk ke layout masing-masing role |
| Ada migration baru | Pastikan `down()` method ada untuk rollback |
| Edit Blade | Ikuti Tailwind classes yang sudah dipakai, jangan tambah CSS sendiri |

---

## 9. FILE STRUCTURE REFERENCE

```
app/
├── Http/Controllers/
│   ├── Admin/       → Admin panel controllers
│   ├── Auth/        → Auth controllers (Breeze)
│   ├── Bidan/       → Bidan section controllers
│   ├── Kader/       → Kader section controllers
│   └── Warga/       → Warga portal controllers
├── Models/          → Eloquent models
├── Services/        → Business logic (WhoZscoreService)
├── Exports/         → Excel export classes
├── Imports/         → Excel import classes
└── View/Components/ → Blade components (layouts)

resources/views/
├── admin/           → Admin views
├── bidan/           → Bidan views
├── kader/           → Kader views
├── warga/           → Warga portal views
├── layouts/         → Layout files per role
├── components/      → Shared blade components
└── auth/            → Auth views (Breeze)

routes/
├── web.php          → Main routes
└── auth.php         → Auth routes
```

---

## 10. FINAL RULES (WAJIB)

1. ✅ **BACA AGENT.md** ini sebelum ngoding apapun
2. ✅ **JANGAN hapus** aturan dari file ini
3. ✅ **TAMBAHKAN** aturan baru jika nemu pola penting
4. ✅ **TEST dulu** sebelum commit (composer run test + npm run build)
5. ✅ **PUSH hanya** setelah test lolos
6. ✅ **CI/CD GAGAL** → analisa → fix → test ulang → push
7. ❌ **JANGAN** push langsung tanpa test
8. ❌ **JANGAN** tambah library baru tanpa diskusi
9. ❌ **JANGAN** ubah migration yang sudah di-push (buat migration baru)
10. ❌ **JANGAN** commit .env, node_modules, vendor
11. ❌ **JANGAN** tambah komentar di kode (~kecuali situasi khusus)
12. ❌ **JANGAN** buat file dokumentasi baru tanpa diminta user

---

*E-Posyandu — Sistem Informasi Posyandu Terpadu*
*Last Updated: 2026-07-05*
