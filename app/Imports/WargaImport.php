<?php

namespace App\Imports;

use App\Models\Warga;
use App\Models\Region;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class WargaImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Skip if NIK already exists
        if (Warga::where('nik', $row['nik'])->exists()) {
            return null;
        }

        // Find Region by Name
        $region = Region::where('name', $row['nama_wilayah'])->first();
        
        if (!$region) {
            throw new \Exception("Wilayah dengan nama '{$row['nama_wilayah']}' tidak ditemukan. Pastikan nama wilayah sesuai dengan Master Data Wilayah.");
        }

        return new Warga([
            'nik' => $row['nik'],
            'nama' => $row['nama_warga'],
            'alamat_lengkap' => $row['alamat_lengkap'] ?? null,
            'nomor_telepon' => $row['nomor_telepon'] ?? null,
            'region_id' => $region->id,
        ]);
    }
}
