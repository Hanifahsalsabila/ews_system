<?php

namespace App\Exports;

use App\Models\SesiPosyandu;
use App\Models\Warga;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SipExport implements FromView, ShouldAutoSize, WithStyles
{
    private $params;

    public function __construct($params)
    {
        $this->params = $params;
    }

    public function view(): View
    {
        $data = [];
        $format_id = $this->params['format_id'];
        $bulan = $this->params['bulan'];
        $tahun = $this->params['tahun'];

        // Basic Filter Mapping Logic based on chosen Format.
        // For production, these should strictly filter by month/year from pivot tables or creation dates.
        if ($format_id == 1 || $format_id == 4) {
            // Ibu Hamil
            $data = Warga::whereHas('ibuHamil')->with(['ibuHamil', 'region.parent.parent'])->get();
        } elseif ($format_id == 2) {
            // Balita
            $data = Warga::whereHas('balita')->with(['balita', 'pemeriksaans', 'region.parent.parent'])->get();
        } elseif ($format_id == 3) {
            // WUS PUS
            $data = Warga::whereDoesntHave('balita')->whereDoesntHave('ibuHamil')->whereHas('pemeriksaans', function ($q) {
                // Approximate filtering
            })->with('region.parent.parent')->get();
            if ($data->isEmpty()) {
                $data = Warga::whereDoesntHave('balita')->whereDoesntHave('ibuHamil')->with('region.parent.parent')->get();
            }
        } elseif ($format_id == 5 || $format_id == 6) {
            // Sesi Posyandu & Operasional
            $data = SesiPosyandu::whereYear('tanggal', $tahun)
                ->whereMonth('tanggal', $bulan)
                ->withCount('absensis', 'pemeriksaans')
                ->get();
        }

        return view('admin.laporan.export', [
            'format_id' => $format_id,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'kepala_desa' => $this->params['kepala_desa'] ?? '',
            'ketua_kader' => $this->params['ketua_kader'] ?? '',
            'data' => $data,
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1 => ['font' => ['bold' => true]],
        ];
    }
}
