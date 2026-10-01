<?php

namespace App\Http\Controllers\Kader;

use App\Http\Controllers\Controller;
use App\Models\Balita;
use App\Models\IbuHamil;
use App\Models\Lansia;
use App\Models\Region;
use App\Models\User;
use App\Models\Warga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class WargaController extends Controller
{
    public function index(Request $request)
    {
        // Only load the hierarchy to display correctly
        $query = Warga::with(['region.parent.parent', 'balita', 'ibuHamil', 'lansia']);

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $wargas = $query->orderBy('nama')->paginate(15);
        $regions = Region::all();

        return view('kader.warga.index', compact('wargas', 'regions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nik' => 'required|string|size:16|unique:warga,nik',
            'nama' => 'required|string|max:255',
            'alamat_lengkap' => 'nullable|string',
            'nomor_telepon' => 'nullable|string|max:20',
            'region_id' => 'required|exists:regions,id',
            'cohort_type' => 'required|in:balita,ibu_hamil,lansia,umum',

            'jenis_kelamin' => 'required_if:cohort_type,balita|nullable|in:L,P',
            'tanggal_lahir' => 'required_if:cohort_type,balita|nullable|date',

            // Balita specific
            'nama_ayah' => 'required_if:cohort_type,balita|nullable|string|max:255',
            'nama_ibu' => 'required_if:cohort_type,balita|nullable|string|max:255',

            // Ibu Hamil specific
            'hpht' => 'required_if:cohort_type,ibu_hamil|nullable|date',
            'hpl' => 'required_if:cohort_type,ibu_hamil|nullable|date',
            'kehamilan_ke' => 'required_if:cohort_type,ibu_hamil|nullable|integer|min:1',

            // Lansia specific
            'golongan_darah' => 'nullable|string|max:5',
            'riwayat_penyakit' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $warga = Warga::create([
                'nik' => $validated['nik'],
                'nama' => $validated['nama'],
                'alamat_lengkap' => $validated['alamat_lengkap'],
                'nomor_telepon' => $validated['nomor_telepon'],
                'region_id' => $validated['region_id'],
                'jenis_kelamin' => $validated['jenis_kelamin'] ?? null,
                'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
            ]);

            if ($validated['cohort_type'] === 'balita') {
                Balita::create([
                    'warga_id' => $warga->id,
                    'nama_ayah' => $validated['nama_ayah'],
                    'nama_ibu' => $validated['nama_ibu'],
                ]);
            } elseif ($validated['cohort_type'] === 'ibu_hamil') {
                IbuHamil::create([
                    'warga_id' => $warga->id,
                    'hpht' => $validated['hpht'],
                    'hpl' => $validated['hpl'],
                    'kehamilan_ke' => $validated['kehamilan_ke'] ?? 1,
                ]);
            } elseif ($validated['cohort_type'] === 'lansia') {
                Lansia::create([
                    'warga_id' => $warga->id,
                    'golongan_darah' => $validated['golongan_darah'],
                    'riwayat_penyakit' => $validated['riwayat_penyakit'],
                ]);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Data Warga berhasil didaftarkan.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()->back()->withErrors(['error' => 'Gagal menyimpan data: '.$e->getMessage()])->withInput();
        }
    }

    public function update(Request $request, Warga $warga)
    {
        $validated = $request->validate([
            'nik' => 'required|string|size:16|unique:warga,nik,'.$warga->id,
            'nama' => 'required|string|max:255',
            'alamat_lengkap' => 'nullable|string',
            'nomor_telepon' => 'nullable|string|max:20',
            'region_id' => 'required|exists:regions,id',

            // Cohort data updates (if exists)
            // Since cohort type is implicit in update based on relations, we use conditional validation if the relation exists, but for simplicity we make them nullable or rely on frontend. Better yet, let's just make them required if they are present in the request or if the warga has a balita relation.
            // A simpler approach: if it's a balita, these must be present.
        ]);

        if ($warga->balita) {
            $request->validate([
                'jenis_kelamin' => 'required|in:L,P',
                'tanggal_lahir' => 'required|date',
                'nama_ayah' => 'required|string|max:255',
                'nama_ibu' => 'required|string|max:255',
            ]);
        }

        DB::beginTransaction();
        try {
            $warga->update([
                'nik' => $validated['nik'],
                'nama' => $validated['nama'],
                'alamat_lengkap' => $validated['alamat_lengkap'],
                'nomor_telepon' => $validated['nomor_telepon'],
                'region_id' => $validated['region_id'],
                'jenis_kelamin' => $validated['jenis_kelamin'] ?? null,
                'tanggal_lahir' => $validated['tanggal_lahir'] ?? null,
            ]);

            if ($warga->balita) {
                $warga->balita->update([
                    'nama_ayah' => $validated['nama_ayah'],
                    'nama_ibu' => $validated['nama_ibu'],
                ]);
            } elseif ($warga->ibuHamil) {
                $warga->ibuHamil->update([
                    'hpht' => $validated['hpht'],
                    'hpl' => $validated['hpl'],
                    'kehamilan_ke' => $validated['kehamilan_ke'],
                ]);
            } elseif ($warga->lansia) {
                $warga->lansia->update([
                    'golongan_darah' => $validated['golongan_darah'],
                    'riwayat_penyakit' => $validated['riwayat_penyakit'],
                ]);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Data Warga berhasil diperbarui.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()->back()->withErrors(['error' => 'Gagal memperbarui data: '.$e->getMessage()])->withInput();
        }
    }

    public function destroy(Warga $warga)
    {
        $warga->delete();

        return redirect()->back()->with('success', 'Data Warga berhasil dihapus.');
    }

    public function generateAccount(Warga $warga)
    {
        if ($warga->user_id) {
            return redirect()->back()->withErrors(['error' => 'Warga ini sudah memiliki akun.']);
        }

        DB::beginTransaction();
        try {
            $email = $warga->nik.'@eposyandu.local';

            // Cek apakah user dengan email atau NIK ini sudah ada
            $user = User::where('email', $email)
                ->orWhere('nik', $warga->nik)
                ->orWhere('username', $warga->nik)
                ->first();

            if (! $user) {
                // Jika belum ada, buat baru
                $user = User::create([
                    'name' => $warga->nama,
                    'email' => $email,
                    'username' => $warga->nik,
                    'nik' => $warga->nik,
                    'password' => Hash::make('posyandu123'),
                    'is_active' => true,
                ]);
                $user->assignRole('orang_tua');
                $message = 'Akun berhasil dibuat. NIK: '.$warga->nik.' / Password: posyandu123';
            } else {
                // Pastikan role orang_tua ada pada user lama
                if (! $user->hasRole('orang_tua')) {
                    $user->assignRole('orang_tua');
                }
                $message = 'Akun sudah ada di sistem dan berhasil dihubungkan ke data Warga.';
            }

            // Hubungkan warga ke user
            $warga->update(['user_id' => $user->id]);

            DB::commit();

            return redirect()->back()->with('success', $message);
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()->back()->withErrors(['error' => 'Gagal membuat akun: '.$e->getMessage()]);
        }
    }
}
