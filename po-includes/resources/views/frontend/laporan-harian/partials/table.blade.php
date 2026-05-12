@php
    $statusBadge = function ($label) {
        return $label === 'Sudah Lapor'
            ? 'bg-secondary-container text-on-secondary-container'
            : 'bg-tertiary-fixed text-on-tertiary-fixed';
    };
@endphp

<div class="overflow-x-auto">
    <table class="min-w-full text-sm">
        <thead class="bg-surface-container-low text-on-surface-variant">
            <tr>
                @if($jenis === 'sekolah')
                    <th class="text-left font-bold px-5 py-4 min-w-[260px]">Nama Sekolah / SPPG</th>
                    <th class="text-left font-bold px-5 py-4">Jenjang</th>
                    <th class="text-left font-bold px-5 py-4 min-w-[180px]">Lokasi</th>
                    <th class="text-left font-bold px-5 py-4">Foto Laporan</th>
                    <th class="text-left font-bold px-5 py-4 min-w-[150px]">Waktu Update</th>
                    <th class="text-left font-bold px-5 py-4">Status</th>
                @else
                    <th class="text-left font-bold px-5 py-4 min-w-[260px]">Nama SPPG</th>
                    <th class="text-left font-bold px-5 py-4 min-w-[180px]">Lokasi</th>
                    <th class="text-left font-bold px-5 py-4">Sekolah Dilayani</th>
                    <th class="text-left font-bold px-5 py-4 min-w-[220px]">Menu Harian</th>
                    <th class="text-left font-bold px-5 py-4 min-w-[150px]">Waktu Update</th>
                    <th class="text-left font-bold px-5 py-4">Status</th>
                @endif
            </tr>
        </thead>
        <tbody class="divide-y divide-surface-container-low">
            @forelse($rows as $row)
                <tr class="hover:bg-surface-container-lowest">
                    @if($jenis === 'sekolah')
                        <td class="px-5 py-4">
                            <a href="{{ $row['detail_url'] }}" class="font-bold text-primary hover:underline">{{ $row['nama'] }}</a>
                            <p class="text-xs text-on-surface-variant mt-1">{{ $row['sppg'] }}</p>
                        </td>
                        <td class="px-5 py-4 text-on-surface-variant">{{ $row['jenjang'] }}</td>
                        <td class="px-5 py-4 text-on-surface-variant">{{ $row['lokasi'] }}</td>
                        <td class="px-5 py-4">
                            @if($row['foto_menu'] || $row['foto_siswa'])
                                <div class="flex items-center gap-2">
                                    @if($row['foto_menu'])
                                        <a href="{{ $row['foto_menu'] }}" data-photo-popup data-photo-title="Foto menu - {{ $row['nama'] }}" class="w-12 h-10 rounded-lg overflow-hidden border border-outline-variant bg-surface-container-low block" title="Foto menu">
                                            <img src="{{ $row['foto_menu'] }}" alt="Foto menu" class="w-full h-full object-cover">
                                        </a>
                                    @endif
                                    @if($row['foto_siswa'])
                                        <a href="{{ $row['foto_siswa'] }}" data-photo-popup data-photo-title="Foto siswa - {{ $row['nama'] }}" class="w-12 h-10 rounded-lg overflow-hidden border border-outline-variant bg-surface-container-low block" title="Foto siswa">
                                            <img src="{{ $row['foto_siswa'] }}" alt="Foto siswa" class="w-full h-full object-cover">
                                        </a>
                                    @endif
                                </div>
                            @else
                                <span class="text-on-surface-variant">-</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-on-surface-variant">{{ $row['waktu_update'] }}</td>
                        <td class="px-5 py-4">
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $statusBadge($row['status_laporan']) }}">{{ $row['status_laporan'] }}</span>
                        </td>
                    @else
                        <td class="px-5 py-4">
                            <a href="{{ $row['detail_url'] }}" class="font-bold text-primary hover:underline">{{ $row['nama'] }}</a>
                            <p class="text-xs text-on-surface-variant mt-1">Kode: {{ $row['kode'] }}</p>
                        </td>
                        <td class="px-5 py-4 text-on-surface-variant">{{ $row['lokasi'] }}</td>
                        <td class="px-5 py-4 font-bold text-primary">{{ number_format($row['jumlah_sekolah'], 0, ',', '.') }}</td>
                        <td class="px-5 py-4 text-on-surface-variant">{{ $row['menu_harian'] }}</td>
                        <td class="px-5 py-4 text-on-surface-variant">{{ $row['waktu_update'] }}</td>
                        <td class="px-5 py-4">
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $statusBadge($row['status_laporan']) }}">{{ $row['status_laporan'] }}</span>
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-5 py-12 text-center text-on-surface-variant">
                        Data laporan harian tidak ditemukan untuk filter ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="px-5 py-4 border-t border-surface-container-low flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <p class="text-sm text-on-surface-variant">
        Menampilkan {{ number_format($rows->count(), 0, ',', '.') }} dari {{ number_format($rows->total(), 0, ',', '.') }} data.
    </p>
    <div class="laporan-pagination">
        {{ $rows->links() }}
    </div>
</div>
