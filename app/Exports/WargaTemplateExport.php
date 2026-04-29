<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class WargaTemplateExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return [
            'NIK',
            'NAMA_WARGA',
            'ALAMAT_LENGKAP',
            'NOMOR_TELEPON',
            'NAMA_WILAYAH'
        ];
    }

    public function array(): array
    {
        return [
            [
                '3501234567890001',
                'Budi Santoso',
                'Jl. Melati No 12',
                '081234567890',
                'RT 01'
            ],
            [
                '3501234567890002',
                'Siti Aminah',
                'Jl. Mawar No 4',
                '081987654321',
                'RT 02'
            ]
        ];
    }
}
