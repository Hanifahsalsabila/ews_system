<?php

namespace App\Imports;

use App\Models\Warga;
use App\Models\Region;
use App\Models\Balita;
use App\Models\IbuHamil;
use App\Models\Lansia;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WargaImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        DB::beginTransaction();
        try {
            foreach ($rows as $row) {
                if (!isset($row['nik']) || empty($row['nik'])) {
                    continue;
                }

                // Skip if NIK already exists
                if (Warga::where('nik', $row['nik'])->exists()) {
                    continue;
                }

                $dusunName = $row['dusun'] ?? '';
                $rwName = $row['rw'] ?? '';
                $rtName = $row['rt'] ?? '';

                $dusun = Region::where('type', 'dusun')->where('name', $dusunName)->first();
                if (!$dusun) throw new \Exception("Dusun dengan nama '{$dusunName}' tidak ditemukan. (Baris NIK: {$row['nik']})");

                $rw = Region::where('type', 'rw')->where('parent_id', $dusun->id)->where('name', $rwName)->first();
                if (!$rw) throw new \Exception("RW '{$rwName}' tidak ditemukan di Dusun '{$dusunName}'. (Baris NIK: {$row['nik']})");

                $rt = Region::where('type', 'rt')->where('parent_id', $rw->id)->where('name', $rtName)->first();
                if (!$rt) throw new \Exception("RT '{$rtName}' tidak ditemukan di RW '{$rwName}'. (Baris NIK: {$row['nik']})");

                $jk = strtoupper(trim($row['jenis_kelamin'] ?? ''));
                if (!in_array($jk, ['L', 'P'])) $jk = null; 
                
                $tglLahir = $row['tanggal_lahir'] ?? null;
                if ($tglLahir) {
                    try {
                        $tglLahir = Carbon::parse($tglLahir)->toDateString();
                    } catch (\Exception $e) {
                        $tglLahir = null;
                    }
                }

                $warga = Warga::create([
                    'nik' => $row['nik'],
                    'nama' => $row['nama_warga'],
                    'alamat_lengkap' => $row['alamat_lengkap'] ?? null,
                    'nomor_telepon' => $row['nomor_telepon'] ?? null,
                    'region_id' => $rt->id,
                    'jenis_kelamin' => $jk,
                    'tanggal_lahir' => $tglLahir,
                ]);

                $kohort = strtolower(trim($row['kohort'] ?? ''));

                if ($kohort === 'balita') {
                    Balita::create([
                        'warga_id' => $warga->id,
                    ]);
                } elseif (in_array($kohort, ['ibu hamil', 'ibu_hamil', 'bumil'])) {
                    IbuHamil::create([
                        'warga_id' => $warga->id,
                    ]);
                } elseif ($kohort === 'lansia') {
                    Lansia::create([
                        'warga_id' => $warga->id,
                    ]);
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
