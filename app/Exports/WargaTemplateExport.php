<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class WargaTemplateExport implements FromArray, WithColumnFormatting, WithHeadings
{
    public function headings(): array
    {
        return [
            'NIK',
            'NAMA_WARGA',
            'ALAMAT_LENGKAP',
            'NOMOR_TELEPON',
            'DUSUN',
            'RW',
            'RT',
            'KOHORT',
            'JENIS_KELAMIN',
            'TANGGAL_LAHIR',
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
                'Mungkid',
                '01',
                '01',
                'Umum',
                '',
                '',
            ],
            [
                '3501234567890002',
                'Siti Aminah',
                'Jl. Mawar No 4',
                '081987654321',
                'Mungkid',
                '01',
                '02',
                'Ibu Hamil',
                'P',
                '1995-05-15',
            ],
            [
                '3501234567890003',
                'Ahmad',
                'Jl. Mawar No 4',
                '',
                'Mungkid',
                '01',
                '02',
                'Balita',
                'L',
                '2024-01-10',
            ],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
            'D' => NumberFormat::FORMAT_TEXT,
            'F' => NumberFormat::FORMAT_TEXT,
            'G' => NumberFormat::FORMAT_TEXT,
            'J' => NumberFormat::FORMAT_TEXT,
        ];
    }
}
