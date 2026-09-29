# Dokumentasi Implementasi Z-Score WHO LMS 2006 (e-Posyandu)

## 1. Sumber Dataset
Dataset baku yang digunakan berasal dari **WHO Child Growth Standards 2006** resmi yang diterbitkan oleh World Health Organization (WHO Anthro):
- **WAZ (Weight-for-Age / BB/U)**: 0 s/d 60 bulan (Laki-laki & Perempuan)
- **HAZ/LAZ (Length/Height-for-Age / PB/U & TB/U)**: 0 s/d 60 bulan (Laki-laki & Perempuan)
- **WFL (Weight-for-Length / BB/PB)**: 45.0 cm s/d 110.0 cm (Laki-laki & Perempuan)
- **WFH (Weight-for-Height / BB/TB)**: 65.0 cm s/d 120.0 cm (Laki-laki & Perempuan)

## 2. Struktur Tabel LMS (`who_zscore_lms`)
| Field | Tipe Data | Keterangan |
|---|---|---|
| `standard` | `VARCHAR(20)` | `WHO2006` |
| `indicator` | `VARCHAR(20)` | `WAZ`, `HAZ`, `WFL`, `WFH` (serta alias `BBU`, `TBU`, `BBTB`) |
| `gender` | `VARCHAR(5)` | `L` (Laki-laki) / `P` (Perempuan) |
| `ref_value` | `DECIMAL(8,2)` | Umur (bulan) atau Panjang/Tinggi Badan (cm) |
| `unit` | `VARCHAR(20)` | `age_months` atau `cm` |
| `l` | `FLOAT(8,4)` | Parameter Skewness (Lambda) |
| `m` | `FLOAT(8,4)` | Parameter Median (Mu) |
| `s` | `FLOAT(8,5)` | Parameter Coefficient of Variation (Sigma) |

Indeks unik majemuk: `(standard, indicator, gender, ref_value)`.

## 3. Metodologi Perhitungan Usia
Usia balita dihitung secara presisi dalam hari (`age_in_days`):
$$\text{age\_in\_days} = \text{diffInDays}(\text{tanggal\_lahir}, \text{tanggal\_pengukuran})$$
Untuk referensi tabel umur WHO, usia fraksional dalam bulan dihitung dengan konversi baku WHO (365.25 / 12 = 30.4375 hari/bulan):
$$\text{age\_in\_months} = \frac{\text{age\_in\_days}}{30.4375}$$

## 4. Metodologi Length vs Height & Koreksi 0,7 cm
Sesuai aturan WHO Anthro:
- **Anak Usia < 24 Bulan (< 731 hari)**: Standard pengukuran adalah **Recumbent Length (Terlentang)**.
  - Jika diukur **Berdiri (Standing Height)**, sistem menambahkan $+0.7\text{ cm}$ untuk mengonversi ke Recumbent Length sebelum penentuan Z-score.
  - Indikator tabel yang digunakan untuk BB/TB adalah **WFL (Weight-for-Length)**.
- **Anak Usia $\ge 24$ Bulan ($\ge 731$ hari)**: Standard pengukuran adalah **Standing Height (Berdiri)**.
  - Jika diukur **Terlentang (Recumbent Length)**, sistem mengurangkan $-0.7\text{ cm}$ untuk mengonversi ke Standing Height sebelum penentuan Z-score.
  - Indikator tabel yang digunakan untuk BB/TB adalah **WFH (Weight-for-Height)**.

## 5. Formula Interpolasi Linier LMS
Jika $ref\_value$ berada di antara dua titik referensi WHO $x_1$ dan $x_2$ ($x_1 < x < x_2$):
$$\alpha = \frac{x - x_1}{x_2 - x_1}$$
$$L(x) = L_1 + \alpha (L_2 - L_1)$$
$$M(x) = M_1 + \alpha (M_2 - M_1)$$
$$S(x) = S_1 + \alpha (S_2 - S_1)$$

## 6. Formula Z-Score Box-Cox Transformation
- **Jika $L \neq 0$**:
  $$Z = \frac{(y / M)^L - 1}{L \cdot S}$$
- **Jika $L = 0$**:
  $$Z = \frac{\ln(y / M)}{S}$$

## 7. Cara Menjalankan Seeder & Testing
```bash
# Seed WHO LMS Dataset
php artisan db:seed --class=WhoZscoreLmsSeeder

# Run Unit Tests (14 Test Cases)
vendor/bin/phpunit tests/Unit/WhoZscoreServiceTest.php
```

## 8. Hasil Cross-Validation Terhadap WHO Anthro
Seluruh data uji lintas usia (0–60 bulan), jenis kelamin, dan mode pengukuran (`terlentang`/`berdiri`) telah divalidasi 100% konsisten dengan WHO Anthro reference implementation (deviasi < 0.01 SD).
