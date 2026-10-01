<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\WargaImport;
use App\Models\Balita;
use App\Models\IbuHamil;
use App\Models\Lansia;
use App\Models\Region;
use App\Models\User;
use App\Models\Warga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class WargaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Warga::with(['region.parent.parent', 'balita', 'ibuHamil', 'lansia']);

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        if ($request->ajax()) {
            return response()->json($query->limit(10)->get());
        }

        $wargas = $query->orderBy('nama')->paginate(15);
        $regions = Region::all();

        return view('admin.warga.index', compact('wargas', 'regions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nik' => 'required|string|size:16|unique:warga,nik',
            'nama' => 'required|string|max:255',
            'alamat_lengkap' => 'nullable|string',
            'nomor_telepon' => 'nullable|string|max:20',
            'region_id' => 'required|exists:regions,id',
            'cohort_type' => 'required|in:balita,ibu_hamil,lansia,umum',

            'jenis_kelamin' => 'nullable|in:L,P',
            'tanggal_lahir' => 'nullable|date',

            // Balita specific
            'nama_ayah' => 'nullable|string|max:255',
            'nama_ibu' => 'nullable|string|max:255',

            // Ibu Hamil specific
            'hpht' => 'nullable|date',
            'hpl' => 'nullable|date',
            'kehamilan_ke' => 'nullable|integer|min:1',

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

    /**
     * Display the specified resource.
     */
    public function show(Warga $warga)
    {
        // We use modals for viewing/editing, so redirect direct access to index
        return redirect()->route('admin.wargas.index')->with('success', 'Menampilkan data warga di tabel.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Warga $warga)
    {
        $validated = $request->validate([
            'nik' => 'required|string|size:16|unique:warga,nik,'.$warga->id,
            'nama' => 'required|string|max:255',
            'alamat_lengkap' => 'nullable|string',
            'nomor_telepon' => 'nullable|string|max:20',
            'region_id' => 'required|exists:regions,id',

            // Cohort data updates (if exists)
            'jenis_kelamin' => 'nullable|in:L,P',
            'tanggal_lahir' => 'nullable|date',
            'nama_ayah' => 'nullable|string|max:255',
            'nama_ibu' => 'nullable|string|max:255',
            'hpht' => 'nullable|date',
            'hpl' => 'nullable|date',
            'kehamilan_ke' => 'nullable|integer|min:1',
            'golongan_darah' => 'nullable|string|max:5',
            'riwayat_penyakit' => 'nullable|string',
        ]);

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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Warga $warga)
    {
        $warga->delete();

        return redirect()->back()->with('success', 'Data Warga berhasil dihapus.');
    }

    /**
     * Import Warga from Excel.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:2048',
        ]);

        try {
            Excel::import(new WargaImport, $request->file('file'));

            return redirect()->back()->with('success', 'Data Warga berhasil diimpor.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Gagal mengimpor data: '.$e->getMessage()]);
        }
    }

    /**
     * Generate Secure QR Code SVG for a given NIK.
     */
    public function qrCode($nik)
    {
        // Encrypt the NIK
        $encryptedToken = Crypt::encryptString($nik);

        // Generate the URL for the QR code
        $url = route('warga.verify', ['token' => $encryptedToken]);

        // Generate QR code containing the secure URL
        return response(QrCode::size(250)->margin(2)->generate($url))
            ->header('Content-Type', 'image/svg+xml');
    }

    /**
     * Verify and show public profile from secure QR token.
     */
    public function verify($token)
    {
        try {
            $nik = Crypt::decryptString($token);
            $warga = Warga::with(['region.parent.parent', 'balita', 'ibuHamil', 'lansia'])->where('nik', $nik)->firstOrFail();

            return view('warga.verify', compact('warga'));
        } catch (\Exception $e) {
            abort(404, 'Kode QR tidak valid atau telah kadaluarsa.');
        }
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
