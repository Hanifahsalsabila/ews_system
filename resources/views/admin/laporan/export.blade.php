<table>
    <!-- Judul / Kop Laporan -->
    <thead>
        <tr>
            <th colspan="5" style="font-size: 14px; font-weight: bold; text-align: center;">LAPORAN SISTEM INFORMASI POSYANDU (SIP) FORMAT {{ $format_id }}</th>
        </tr>
        <tr>
            <th colspan="5" style="font-size: 12px; text-align: center;">Bulan: {{ str_pad($bulan, 2, '0', STR_PAD_LEFT) }} | Tahun: {{ $tahun }}</th>
        </tr>
        <tr>
            <th colspan="5"></th> <!-- Spacer -->
        </tr>

        <!-- Header Kolom Berdasarkan Format SIP -->
        @if($format_id == 1 || $format_id == 4)
            <tr>
                <th style="font-weight: bold; border: 1px solid #000; background-color: #f3f4f6;">No</th>
                <th style="font-weight: bold; border: 1px solid #000; background-color: #f3f4f6;">NIK Warga</th>
                <th style="font-weight: bold; border: 1px solid #000; background-color: #f3f4f6;">Nama Ibu Hamil</th>
                <th style="font-weight: bold; border: 1px solid #000; background-color: #f3f4f6;">HPHT (Haid Terakhir)</th>
                <th style="font-weight: bold; border: 1px solid #000; background-color: #f3f4f6;">HPL (Perkiraan Lahir)</th>
                <th style="font-weight: bold; border: 1px solid #000; background-color: #f3f4f6;">Kehamilan Ke</th>
                <th style="font-weight: bold; border: 1px solid #000; background-color: #f3f4f6;">Alamat</th>
            </tr>
        @elseif($format_id == 2)
            <tr>
                <th style="font-weight: bold; border: 1px solid #000; background-color: #f3f4f6;">No</th>
                <th style="font-weight: bold; border: 1px solid #000; background-color: #f3f4f6;">No KMS / NIK</th>
                <th style="font-weight: bold; border: 1px solid #000; background-color: #f3f4f6;">Nama Balita/Bayi</th>
                <th style="font-weight: bold; border: 1px solid #000; background-color: #f3f4f6;">Jenis Kelamin</th>
                <th style="font-weight: bold; border: 1px solid #000; background-color: #f3f4f6;">Tanggal Lahir</th>
                <th style="font-weight: bold; border: 1px solid #000; background-color: #f3f4f6;">Nama Ayah</th>
                <th style="font-weight: bold; border: 1px solid #000; background-color: #f3f4f6;">Nama Ibu</th>
                <th style="font-weight: bold; border: 1px solid #000; background-color: #f3f4f6;">Alamat</th>
            </tr>
        @elseif($format_id == 3)
            <tr>
                <th style="font-weight: bold; border: 1px solid #000; background-color: #f3f4f6;">No</th>
                <th style="font-weight: bold; border: 1px solid #000; background-color: #f3f4f6;">NIK Warga</th>
                <th style="font-weight: bold; border: 1px solid #000; background-color: #f3f4f6;">Nama WUS/PUS</th>
                <th style="font-weight: bold; border: 1px solid #000; background-color: #f3f4f6;">Alamat</th>
                <th style="font-weight: bold; border: 1px solid #000; background-color: #f3f4f6;">No Telepon</th>
            </tr>
        @elseif($format_id == 5 || $format_id == 6)
            <tr>
                <th style="font-weight: bold; border: 1px solid #000; background-color: #f3f4f6;">No</th>
                <th style="font-weight: bold; border: 1px solid #000; background-color: #f3f4f6;">Kode Sesi</th>
                <th style="font-weight: bold; border: 1px solid #000; background-color: #f3f4f6;">Nama Sesi Kegiatan</th>
                <th style="font-weight: bold; border: 1px solid #000; background-color: #f3f4f6;">Tanggal Pelaksanaan</th>
                <th style="font-weight: bold; border: 1px solid #000; background-color: #f3f4f6;">Lokasi</th>
                <th style="font-weight: bold; border: 1px solid #000; background-color: #f3f4f6;">Total Warga Hadir (Absensi)</th>
                <th style="font-weight: bold; border: 1px solid #000; background-color: #f3f4f6;">Total Tindakan/Pemeriksaan</th>
            </tr>
        @endif
    </thead>

    <!-- Data Baris -->
    <tbody>
        @foreach($data as $index => $row)
            @if($format_id == 1 || $format_id == 4)
                <tr>
                    <td style="border: 1px solid #000;">{{ $index + 1 }}</td>
                    <td style="border: 1px solid #000;">'{{ $row->nik }}</td>
                    <td style="border: 1px solid #000;">{{ $row->nama }}</td>
                    <td style="border: 1px solid #000;">{{ $row->ibuHamil ? $row->ibuHamil->hpht : '-' }}</td>
                    <td style="border: 1px solid #000;">{{ $row->ibuHamil ? $row->ibuHamil->hpl : '-' }}</td>
                    <td style="border: 1px solid #000;">{{ $row->ibuHamil ? $row->ibuHamil->kehamilan_ke : '-' }}</td>
                    <td style="border: 1px solid #000;">{{ $row->alamat_lengkap }}</td>
                </tr>
            @elseif($format_id == 2)
                <tr>
                    <td style="border: 1px solid #000;">{{ $index + 1 }}</td>
                    <td style="border: 1px solid #000;">'{{ $row->nik }}</td>
                    <td style="border: 1px solid #000;">{{ $row->nama }}</td>
                    <td style="border: 1px solid #000;">{{ $row->balita ? ($row->balita->jenis_kelamin == 'L' ? 'Laki-Laki' : 'Perempuan') : '-' }}</td>
                    <td style="border: 1px solid #000;">{{ $row->balita ? $row->balita->tanggal_lahir : '-' }}</td>
                    <td style="border: 1px solid #000;">{{ $row->balita ? $row->balita->nama_ayah : '-' }}</td>
                    <td style="border: 1px solid #000;">{{ $row->balita ? $row->balita->nama_ibu : '-' }}</td>
                    <td style="border: 1px solid #000;">{{ $row->alamat_lengkap }}</td>
                </tr>
            @elseif($format_id == 3)
                <tr>
                    <td style="border: 1px solid #000;">{{ $index + 1 }}</td>
                    <td style="border: 1px solid #000;">'{{ $row->nik }}</td>
                    <td style="border: 1px solid #000;">{{ $row->nama }}</td>
                    <td style="border: 1px solid #000;">{{ $row->alamat_lengkap }}</td>
                    <td style="border: 1px solid #000;">{{ $row->nomor_telepon }}</td>
                </tr>
            @elseif($format_id == 5 || $format_id == 6)
                <tr>
                    <td style="border: 1px solid #000;">{{ $index + 1 }}</td>
                    <td style="border: 1px solid #000;">{{ $row->id }}</td>
                    <td style="border: 1px solid #000;">{{ $row->nama_sesi }}</td>
                    <td style="border: 1px solid #000;">{{ $row->tanggal_pelaksanaan }}</td>
                    <td style="border: 1px solid #000;">{{ $row->lokasi }}</td>
                    <td style="border: 1px solid #000; text-align: center;">{{ $row->absensis_count ?? 0 }}</td>
                    <td style="border: 1px solid #000; text-align: center;">{{ $row->pemeriksaans_count ?? 0 }}</td>
                </tr>
            @endif
        @endforeach

        @if(count($data) === 0)
        <tr>
            <td colspan="7" style="text-align: center; border: 1px solid #000; font-style: italic;">
                Tidak ada data pada periode bulan dan tahun ini.
            </td>
        </tr>
        @endif
    </tbody>

    <!-- Tanda Tangan / Pengesahan -->
    <tfoot>
        <tr>
            <td colspan="5"></td>
        </tr>
        <tr>
            <td colspan="5"></td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center;">Berdasarkan Pemeriksaan Aktual,</td>
            <td colspan="2"></td>
            <td colspan="3" style="text-align: center;">Mengetahui,</td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center;">Ketua Kader Posyandu</td>
            <td colspan="2"></td>
            <td colspan="3" style="text-align: center;">Kepala Desa</td>
        </tr>
        <tr>
            <td colspan="5"></td>
        </tr>
        <tr>
            <td colspan="5"></td>
        </tr>
        <tr>
            <td colspan="5"></td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: center; font-weight: bold; text-decoration: underline;">{{ $ketua_kader != '' ? $ketua_kader : '(.................................)' }}</td>
            <td colspan="2"></td>
            <td colspan="3" style="text-align: center; font-weight: bold; text-decoration: underline;">{{ $kepala_desa != '' ? $kepala_desa : '(.................................)' }}</td>
        </tr>
    </tfoot>
</table>
