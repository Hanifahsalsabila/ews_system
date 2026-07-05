# E-Posyandu

[![Test](https://github.com/Hanifahsalsabila/uas-posyandu/actions/workflows/test.yml/badge.svg)](https://github.com/Hanifahsalsabila/uas-posyandu/actions/workflows/test.yml)
![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?logo=php)
![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?logo=laravel)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3-06B6D4?logo=tailwindcss)
![License](https://img.shields.io/badge/License-MIT-green)

**Sistem Informasi Posyandu Terpadu** — Platform digital untuk manajemen posyandu, pemantauan tumbuh kembang balita, ibu hamil, dan lansia berbasis web.

---

## Fitur Utama

### 👨‍💼 Super Admin
| Modul | Fitur |
|-------|-------|
| **Dashboard** | Overview data warga, grafik kunjungan, audit trail |
| **Manajemen User** | CRUD petugas, toggle status, filter role |
| **Data Warga** | CRUD + import Excel + QR Code + Generate Akun |
| **Wilayah** | Hierarki Dusun/RW/RT |
| **Sesi Posyandu** | Jadwal + mode Operasional realtime |
| **Master Vaksin** | CRUD stok, batch, kadaluarsa |
| **Inventaris Alat** | CRUD kondisi alat kesehatan |
| **Pelaporan** | Export Excel (SIP, Warga, Balita, Ibu Hamil, Lansia, Kunjungan) |
| **Audit Trail** | Riwayat perubahan data |

### 🩺 Puskesmas / Bidan
| Modul | Fitur |
|-------|-------|
| **Dashboard** | Tren pemeriksaan, sebaran stunting, grafik kohort |
| **Pemeriksaan** | Validasi data balita/ibu hamil/lansia, input klinis |
| **KMS & Stunting** | Grafik KMS, deteksi Z-Score WHO, status gizi |
| **Kunjungan Rumah** | EWS otomatis (2T), claim & tindak lanjut |
| **Penyuluhan** | CRUD + publish artikel edukasi |
| **Logistik** | Stok vaksin & inventaris |
| **Pelaporan** | Export coverage & data kohort |

### 👥 Kader Posyandu
| Modul | Fitur |
|-------|-------|
| **Dashboard** | Sesi aktif hari ini, statistik antrean |
| **Meja 1** | Scan QR / input NIK, absensi warga |
| **Meja 2 & 3** | Input pengukuran (BB, TB, LK, LL) + EWS |
| **Buku Register** | Data warga + generate akun portal |
| **Penyuluhan** | Lihat artikel edukasi yang dipublikasi |

### 👶 Warga / Orang Tua
| Modul | Fitur |
|-------|-------|
| **Dashboard** | Data anak, riwayat imunisasi, jadwal |
| **KMS Digital** | Grafik tumbuh kembang, Z-Score |
| **Riwayat** | Riwayat pemeriksaan & kunjungan |
| **Badges** | Lencana keaktifan (3/6/12 bulan) |
| **Edukasi** | Artikel penyuluhan interaktif |

---

## Tech Stack

| Layer | Teknologi |
|-------|-----------|
| **Backend** | PHP 8.3, Laravel 13, Spatie Permission, Laravel Auditing |
| **Frontend** | Blade, Tailwind CSS 3, Alpine.js, Chart.js |
| **Database** | MySQL 8 (production), SQLite (testing) |
| **Build** | Vite 8, PostCSS, Autoprefixer |
| **Export** | Laravel Excel (Maatwebsite) |
| **QR Code** | simple-qrcode + Crypt |
| **Testing** | PHPUnit 12 |
| **CI/CD** | GitHub Actions |

---

## Arsitektur

```
app/
├── Http/Controllers/
│   ├── Admin/       → Panel Super Admin
│   ├── Auth/        → Authentikasi (Breeze)
│   ├── Bidan/       → Fitur Puskesmas/Bidan
│   ├── Kader/       → Fitur Kader Posyandu
│   └── Warga/       → Portal Orang Tua
├── Models/          → Eloquent ORM (15 model)
├── Services/        → Z-Score WHO 2006
├── Exports/         → 6 format Excel
└── Imports/         → Import data warga

resources/views/
├── admin/           → 10 modul Super Admin
├── bidan/           → 10 modul Puskesmas
├── kader/           → 7 modul Kader
├── warga/           → 6 modul Portal Warga
└── layouts/         → 6 layout per role
```

### Role-Based Routing

```
/                   → Public (welcome page)
/auth/*             → Login, Register, Reset Password
/admin/*            → Super Admin
/bidan/*            → Puskesmas/Bidan
/kader/*            → Kader Posyandu
/portal/*           → Warga/Orang Tua
/v/{token}          → Verifikasi QR Publik
```

### Database Schema

```
users (1) ── (0..1) warga ── (0..1) balita
                          ├── (0..1) ibu_hamil
                          ├── (0..1) lansia
                          ├── (0..*) pemeriksaan ── (0..1) kalkulasi_zscore
                          ├── (0..*) absensi
                          └── (0..*) kunjungan_rumah

regions ── self-referencing (dusun/rw/rt) ── warga
sesi_posyandu ── absensi, pemeriksaan
users ── penyuluhan (penulis)
```

### Flow Operasional Posyandu

```
Kader Buka Sesi → Warga Scan QR → Absensi
    ↓
Kader Ukur BB/TB/LK → Pemeriksaan Awal
    ↓
EWS Deteksi 2T? → Auto-create Kunjungan Rumah
    ↓
Bidan Validasi → Klinis + Z-Score → Selesai
```

---

## Screenshots

> *(Tambahkan screenshot di sini)*

| Role | Preview |
|------|---------|
| Super Admin Dashboard | `screenshots/admin-dashboard.png` |
| Bidan Pemeriksaan | `screenshots/bidan-pemeriksaan.png` |
| Kader Pengukuran | `screenshots/kader-pengukuran.png` |
| Portal Warga | `screenshots/warga-dashboard.png` |

---

## Quick Start

### Prerequisites

- PHP 8.3+
- Composer 2.x
- Node.js 20+
- MySQL 8+
- SQLite (testing)

### Installation

```bash
git clone https://github.com/Hanifahsalsabila/uas-posyandu.git
cd uas-posyandu

# Setup otomatis
composer run setup

# Atau manual:
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install --ignore-scripts
npm run build
```

### Development Server

```bash
composer run dev
```

Menjalankan 4 proses concurrently:
- `php artisan serve` — HTTP Server
- `php artisan queue:listen` — Queue Worker
- `php artisan pail` — Log Viewer
- `npm run dev` — Vite HMR

### Running Tests

```bash
composer run test
```

Atau:

```bash
php artisan config:clear
php artisan test
```

---

## Deployment

### Production Checklist

1. Set `APP_ENV=production`, `APP_DEBUG=false`
2. Database: MySQL with `utf8mb4`
3. Cache: `php artisan optimize`
4. Queue: Supervisor for `queue:work`
5. Storage: Symlink `php artisan storage:link`

### Recommended Stack

| Komponen | Rekomendasi |
|----------|-------------|
| Web Server | Nginx + PHP-FPM 8.3 |
| Database | MySQL 8 / MariaDB 10 |
| Cache | Redis / File |
| Queue | Database / Redis |
| SSL | Let's Encrypt |

---

## Agentic Development

Dokumen **[AGENT.md](AGENT.md)** berisi panduan lengkap untuk AI coding agents (OpenCode, Claude Code, Cursor, GitHub Copilot):

- Arsitektur & routing
- Role access matrix
- Coding standards
- Workflow: edit → test → commit → CI
- Known gaps & bugs

---

## Kontribusi

1. Baca [AGENT.md](AGENT.md) untuk standar kode
2. Buat branch: `feat/nama-fitur` atau `fix/nama-bug`
3. Test lokal: `composer run test`
4. Build: `npm run build`
5. Commit dengan format: `feat/fix: pesan jelas`
6. Push → CI auto-test → PR

---

## Lisensi

MIT License — open source untuk pengembangan Posyandu di Indonesia.

---

*Dibangun dengan ❤️ untuk layanan kesehatan ibu dan anak Indonesia.*
