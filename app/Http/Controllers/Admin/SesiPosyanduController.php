<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\KalkulasiZScore;
use App\Models\Pemeriksaan;
use App\Models\SesiPosyandu;
use App\Models\Warga;
use App\Services\WhoZscoreService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SesiPosyanduController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sessions = SesiPosyandu::orderBy('tanggal', 'desc')->get();

        return view('admin.sesi.index', compact('sessions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_sesi' => 'required|string|max:255',
            'tanggal' => 'required|date',
        ]);

        SesiPosyandu::create([
            'nama_sesi' => $validated['nama_sesi'],
            'tanggal' => $validated['tanggal'],
            'status' => 'aktif',
        ]);

        return redirect()->back()->with('success', 'Sesi Posyandu berhasil dibuka.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SesiPosyandu $sesiPosyandu)
    {
        $validated = $request->validate([
            'nama_sesi' => 'required|string|max:255',
            'tanggal' => 'required|date',
        ]);

        $sesiPosyandu->update($validated);

        return redirect()->back()->with('success', 'Sesi Posyandu berhasil diperbarui.');
    }

    /**
     * Toggle the status of a session (Buka/Tutup Sesi).
     */
    public function toggleStatus(SesiPosyandu $sesi)
    {
        $sesi->status = ($sesi->status === 'aktif') ? 'selesai' : 'aktif';
        $sesi->save();

        $message = $sesi->status === 'aktif' ? 'Sesi berhasil dibuka kembali.' : 'Sesi berhasil ditutup (Selesai).';

        return redirect()->back()->with('success', $message);
    }

    /**
     * Display the operasional dashboard for a specific session.
     */
    public function operasional(SesiPosyandu $sesi)
    {
        // Load relationships to show recent activities
        $sesi->load(['absensis.warga', 'pemeriksaans.warga']);

        return view('admin.sesi.operasional', compact('sesi'));
    }

    /**
     * Store measurement and attendance from Operasional Meja.
     */
    public function storeOperasional(Request $request, SesiPosyandu $sesi)
    {
        $validated = $request->validate([
            'warga_id' => 'required|exists:warga,id',
            'berat_badan' => 'nullable|numeric',
            'tinggi_badan' => 'nullable|numeric',
            'lingkar_kepala' => 'nullable|numeric',
            'lingkar_lengan' => 'nullable|numeric',
            'tensi_darah' => 'nullable|string',
            'keluhan' => 'nullable|string',
            'tindakan' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        $warga = Warga::with(['balita'])->find($validated['warga_id']);

        DB::beginTransaction();
        try {
            // Meja 1: Absensi (Check-in)
            Absensi::firstOrCreate([
                'warga_id' => $validated['warga_id'],
                'sesi_posyandu_id' => $sesi->id,
            ]);

            // Meja 2-5: Pemeriksaan
            $pemeriksaan = Pemeriksaan::updateOrCreate(
                [
                    'warga_id' => $validated['warga_id'],
                    'sesi_posyandu_id' => $sesi->id,
                ],
                array_merge($validated, [
                    'petugas_id' => auth()->id(),
                    'is_validated' => true,
                ])
            );

            // Enterprise Feature: Smart Analytics (WHO Z-Score)
            if ($warga->balita && ! empty($validated['berat_badan']) && ! empty($validated['tinggi_badan'])) {
                $dob = $warga->tanggal_lahir;
                $sessionDate = $sesi->tanggal;
                $gender = $warga->jenis_kelamin;
                $posisiUkur = $request->input('posisi_ukur', 'terlentang');

                $zscoreService = new WhoZscoreService;

                $bbuRes = $zscoreService->calculateBBU($validated['berat_badan'], $dob, $sessionDate, $gender);
                $tbuRes = $zscoreService->calculateTBU($validated['tinggi_badan'], $dob, $sessionDate, $gender, $posisiUkur);
                $bbtbRes = $zscoreService->calculateBBTB($validated['berat_badan'], $validated['tinggi_badan'], $dob, $sessionDate, $gender, $posisiUkur);

                $zBbu = $bbuRes['zscore'];
                $zTbu = $tbuRes['zscore'];
                $zBbTb = $bbtbRes['zscore'];

                KalkulasiZScore::updateOrCreate(
                    ['pemeriksaan_id' => $pemeriksaan->id],
                    [
                        'zscore_bb_u' => $zBbu,
                        'raw_zscore_bb_u' => $bbuRes['raw_zscore'] ?? null,
                        'flag_bb_u' => $bbuRes['flag'] ?? null,
                        'zscore_tb_u' => $zTbu,
                        'raw_zscore_tb_u' => $tbuRes['raw_zscore'] ?? null,
                        'flag_tb_u' => $tbuRes['flag'] ?? null,
                        'zscore_bb_tb' => $zBbTb,
                        'raw_zscore_bb_tb' => $bbtbRes['raw_zscore'] ?? null,
                        'flag_bb_tb' => $bbtbRes['flag'] ?? null,
                        'status_gizi' => $bbuRes['status'],
                        'status_stunting' => $tbuRes['status'],
                        'status_bb_tb' => $bbtbRes['status'],
                        'age_in_days' => $bbuRes['age_in_days'] ?? $tbuRes['age_in_days'],
                        'measurement_type' => $tbuRes['measurement_type'] ?? 'LENGTH',
                        'source_version' => 'who_anthro_v1.1.0',
                    ]
                );
            }

            DB::commit();

            return response()->json(['message' => 'Data berhasil disimpan dan divalidasi.', 'data' => $pemeriksaan]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json(['message' => 'Gagal menyimpan: '.$e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SesiPosyandu $sesiPosyandu)
    {
        $sesiPosyandu->delete();

        return redirect()->back()->with('success', 'Sesi Posyandu berhasil dihapus.');
    }
}
