@extends(getTheme('layouts.app'))

@section('content')
@php
    $statusSekolah = [
        1 => ['Aktif', 'bg-secondary text-white'],
        2 => ['Tidak Aktif', 'bg-yellow-100 text-yellow-800'],
        3 => ['Menolak', 'bg-red-100 text-red-800'],
    ][$sekolah->status_layanan] ?? ['-', 'bg-surface-container text-on-surface'];

    $menuFoto = $menuHariIni && $menuHariIni->foto
        ? asset('po-content/uploads/'.$menuHariIni->foto)
        : ($laporanSekolahHariIni && $laporanSekolahHariIni->foto_menu ? asset('po-content/uploads/'.$laporanSekolahHariIni->foto_menu) : null);
    $menuNama = $menuHariIni->nama ?? ($laporanSekolahHariIni ? 'Laporan menu sekolah' : 'Menu belum tersedia');
    $menuDeskripsi = $menuHariIni->deskripsi ?? ($laporanSekolahHariIni ? 'Menu dilaporkan oleh sekolah. Data menu harian SPPG belum tersedia.' : 'Belum ada data menu harian untuk SPPG yang melayani sekolah ini.');
    $hasSekolahCoordinate = !empty($sekolah->latitude) && !empty($sekolah->longitude);
    $hasSppgCoordinate = $sppgUtama && !empty($sppgUtama->latitude) && !empty($sppgUtama->longitude);
@endphp
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
    #sekolah-map {
        height: 24rem;
        width: 100%;
    }
</style>
<section class="pt-32 pb-20 px-6 max-w-7xl mx-auto">
    <div class="max-w-6xl mx-auto space-y-8">
        <!-- School Identity Header -->
        <section class="relative overflow-hidden rounded-3xl bg-primary text-white p-12 flex flex-col md:flex-row justify-between items-end gap-6 shadow-2xl">
            <div class="relative z-10 space-y-4 max-w-2xl">
                <div class="flex items-center gap-3">
                    <span class="bg-primary-container text-white text-xs font-bold px-3 py-1 rounded-full tracking-wider uppercase">{{ $jenisSekolah }}</span>
                    <span class="{{ $statusSekolah[1] }} text-xs font-bold px-3 py-1 rounded-full tracking-wider uppercase flex items-center gap-1">
                        <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">verified</span>
                        {{ $statusSekolah[0] }}
                    </span>
                </div>
                <h1 class="text-5xl font-extrabold font-headline leading-tight">{{ $sekolah->nama }}</h1>
                <div class="flex items-start gap-2 text-on-primary-container text-lg max-w-lg">
                    <span class="material-symbols-outlined mt-1">location_on</span>
                    <p>
                        {{ $sekolah->alamat ?: '-' }}
                        @if($sekolah->wilayah)
                            <br>{{ $sekolah->wilayah->nama_wilayah }}
                            @if($sekolah->wilayah->parent)
                                , {{ $sekolah->wilayah->parent->nama_wilayah }}
                            @endif
                        @endif
                    </p>
                </div>
            </div>
            <div class="absolute right-0 top-0 w-1/3 h-full opacity-10 pointer-events-none">
                <img class="w-full h-full object-cover" data-alt="minimalist architectural abstract with clean lines and soft blue geometric shadows" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDUk306NgS9CjXyChJM47eZLTu-3zEov1GUOzsfmhUb5ZYCxaneN5qHMEKKsSAXIyn1l5t4q0-ujnZhoNFfQVGcYX6yWNCrWG27OSkYFHvBQRSFExXbNH3VGytqSoviavXu2FUseOWm2V92j3-Fel6vz7Le5fcbZ84ykBJs6gK2FBXZ3BQ2-giY_ZqR--csAOUsQxrCGvDldR-XMqzTEia_eRbhfAFce2vYsi3t13yqrXEOifyFmEifNWrsGwZaBKAXENoAk5EDpZCd" />
            </div>
        </section>
        <!-- Bento Grid Layout -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Distribution Status -->
            <div class="md:col-span-2 bg-surface-container-low rounded-3xl p-8 flex flex-col justify-between relative overflow-hidden group">
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold font-headline text-primary">Status Laporan MBG</h3>
                        <span class="text-label text-xs font-semibold uppercase tracking-widest text-on-surface-variant">Real-time update</span>
                    </div>
                    <div class="flex items-center gap-6">
                        <div class="bg-secondary-container text-on-secondary-container p-6 rounded-2xl flex items-center gap-4">
                            <div class="relative flex h-4 w-4">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-secondary opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-4 w-4 bg-secondary"></span>
                            </div>
                        <span class="text-2xl font-bold">{{ $statusDistribusi['label'] }}</span>
                        </div>
                        <div class="text-on-surface-variant">
                            <p class="text-sm">Update Terakhir</p>
                            <p class="text-xl font-bold text-primary">{{ $statusDistribusi['time'] }}</p>
                        </div>
                    </div>
                </div>
                <div class="mt-8 pt-6 border-t border-outline-variant/20 flex gap-8">
                    <div>
                        <p class="text-xs font-medium text-outline uppercase tracking-wider">Total Porsi</p>
                        <p class="text-lg font-bold">{{ number_format($totalPorsiHariIni ?? 0, 0, ',', '.') }} Porsi</p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-outline uppercase tracking-wider">Tanggal</p>
                        <p class="text-lg font-bold text-secondary">{{ $tanggalLaporanHariIni ? $tanggalLaporanHariIni->format('d M Y') : '-' }}</p>
                    </div>
                </div>
            </div>
            <!-- Student Total Card -->
            <div class="bg-surface-container-lowest rounded-3xl p-8 shadow-[0px_12px_32px_rgba(24,28,30,0.06)] flex flex-col items-center justify-center text-center space-y-4 border-l-4 border-tertiary-fixed">
                <div class="bg-primary-fixed w-16 h-16 rounded-2xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-primary text-3xl" style="font-variation-settings: 'FILL' 1;">groups</span>
                </div>
                <div>
                    <p class="text-sm font-medium text-on-surface-variant mb-1 uppercase tracking-widest">Total Penerima</p>
                    <h4 class="text-5xl font-extrabold text-primary">{{ number_format($sekolah->jumlah_total, 0, ',', '.') }}</h4>
                    <p class="text-on-surface-variant font-medium">
                        @if($penerimas->has('siswa'))
                            {{ number_format($penerimas['siswa'], 0, ',', '.') }} Siswa
                        @else
                            Bumil {{ number_format($penerimas['bumil'] ?? 0, 0, ',', '.') }},
                            Busui {{ number_format($penerimas['busui'] ?? 0, 0, ',', '.') }},
                            Balita {{ number_format($penerimas['balita'] ?? 0, 0, ',', '.') }}
                        @endif
                    </p>
                </div>
            </div>
            <!-- Daily Menu Section -->
            <div class="md:col-span-2 bg-surface-container-lowest rounded-3xl overflow-hidden shadow-[0px_12px_32px_rgba(24,28,30,0.06)] flex flex-col md:flex-row">
                <div class="md:w-2/5 relative">
                    @if($menuFoto)
                        <img class="h-full w-full object-cover" src="{{ $menuFoto }}" alt="{{ $menuNama }}" />
                    @else
                        <div class="h-full min-h-[260px] w-full bg-surface-container flex items-center justify-center text-on-surface-variant">
                            <span class="material-symbols-outlined text-6xl">restaurant</span>
                        </div>
                    @endif
                    <div class="absolute top-4 left-4 bg-tertiary text-white px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest">Menu Hari Ini</div>
                </div>
                <div class="md:w-3/5 p-8 space-y-6">
                    <div>
                        <h3 class="text-3xl font-extrabold font-headline text-primary">{{ $menuNama }}</h3>
                        <p class="text-on-surface-variant mt-2">{{ $menuDeskripsi }}</p>
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="bg-surface-container p-3 rounded-xl">
                            <p class="text-[10px] uppercase font-bold text-outline">Kalori</p>
                            <p class="text-lg font-bold text-primary">{{ number_format($menuHariIni->besar_energi ?? $menuHariIni->kecil_energi ?? 0, 0, ',', '.') }} <span class="text-xs font-normal">kcal</span></p>
                        </div>
                        <div class="bg-surface-container p-3 rounded-xl">
                            <p class="text-[10px] uppercase font-bold text-outline">Protein</p>
                            <p class="text-lg font-bold text-primary">{{ number_format($menuHariIni->besar_protein ?? $menuHariIni->kecil_protein ?? 0, 0, ',', '.') }} <span class="text-xs font-normal">g</span></p>
                        </div>
                        <div class="bg-surface-container p-3 rounded-xl">
                            <p class="text-[10px] uppercase font-bold text-outline">Serat</p>
                            <p class="text-lg font-bold text-primary">{{ number_format($menuHariIni->besar_serat ?? $menuHariIni->kecil_serat ?? 0, 0, ',', '.') }} <span class="text-xs font-normal">g</span></p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Kitchen Info -->
            <div class="bg-surface-container-low rounded-3xl p-8 space-y-6">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-primary bg-primary-fixed p-2 rounded-lg">soup_kitchen</span>
                    <h3 class="text-xl font-bold font-headline text-primary">Dapur Satuan Pelayanan</h3>
                </div>
                <div class="space-y-4">
                    <div>
                        <h4 class="text-lg font-bold">{{ $sppgUtama->nama ?? '-' }}</h4>
                        <p class="text-sm text-on-surface-variant">{{ $sppgUtama->alamat ?? 'Alamat SPPG belum tersedia' }}</p>
                    </div>
                    <div class="space-y-2">
                        <div class="flex items-center gap-2 text-sm">
                            <span class="material-symbols-outlined text-secondary text-sm" style="font-variation-settings: 'FILL' 1;">verified</span>
                            <span>SLHS: {{ $sppgUtama->slhs_nomor ?? '-' }}</span>
                        </div>
                    </div>
                    @if($sppgUtama)
                        <a href="{{ route('sppg.detail', Hashids::encode($sppgUtama->id)) }}" class="w-full py-2 border-2 border-primary text-primary font-bold rounded-xl text-sm hover:bg-primary hover:text-white transition-all inline-flex items-center justify-center gap-2">
                            Detail SPPG
                            <span class="material-symbols-outlined text-base">arrow_forward</span>
                        </a>
                    @else
                        <button class="w-full py-2 border-2 border-primary text-primary font-bold rounded-xl text-sm" disabled>SPPG belum tersedia</button>
                    @endif
                </div>
            </div>
        </div>
        <section class="bg-surface-container-lowest rounded-3xl p-8 shadow-[0px_12px_32px_rgba(24,28,30,0.06)]">
            @php
                $riwayatQuery = request()->except(['distribusi_page', 'laporan_page']);
                $distribusiTabUrl = url()->current().'?'.http_build_query(array_merge($riwayatQuery, ['riwayat_tab' => 'distribusi']));
                $laporanTabUrl = url()->current().'?'.http_build_query(array_merge($riwayatQuery, ['riwayat_tab' => 'laporan']));
                $resetFilterUrl = url()->current().'?'.http_build_query(['riwayat_tab' => $activeRiwayatTab]);
            @endphp
            <div class="flex flex-col gap-5 md:flex-row md:items-end md:justify-between mb-5">
                <div>
                    <h3 class="text-2xl font-bold font-headline text-primary">Riwayat MBG</h3>
                    <div class="mt-4 inline-flex rounded-xl bg-surface-container p-1">
                        <a href="{{ $laporanTabUrl }}" class="px-4 py-2 rounded-lg text-sm font-bold {{ $activeRiwayatTab === 'laporan' ? 'bg-primary text-white' : 'text-on-surface-variant' }}">
                            Laporan Sekolah
                        </a>
                        <a href="{{ $distribusiTabUrl }}" class="px-4 py-2 rounded-lg text-sm font-bold {{ $activeRiwayatTab === 'distribusi' ? 'bg-primary text-white' : 'text-on-surface-variant' }}">
                            Distribusi SPPG
                        </a>
                    </div>
                </div>
                <form method="GET" action="{{ url()->current() }}" class="flex flex-col gap-2 sm:flex-row sm:items-end">
                    <input type="hidden" name="riwayat_tab" value="{{ $activeRiwayatTab }}">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-1">Filter Tanggal</label>
                        <input type="date" name="riwayat_tanggal" value="{{ $riwayatTanggal }}" class="h-10 rounded-lg border border-outline-variant bg-white px-3 text-sm">
                    </div>
                    <button type="submit" class="h-10 px-4 rounded-lg bg-primary text-white text-sm font-bold">Filter</button>
                    <a href="{{ $resetFilterUrl }}" class="h-10 px-4 rounded-lg bg-surface-container text-primary text-sm font-bold inline-flex items-center">Reset</a>
                </form>
            </div>

            @if($activeRiwayatTab === 'distribusi')
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-xs uppercase tracking-widest text-on-surface-variant">
                                <th class="py-3">Tanggal</th>
                                <th class="py-3">Menu</th>
                                <th class="py-3">SPPG</th>
                                <th class="py-3">Porsi</th>
                                <th class="py-3">Laporan Sekolah</th>
                                <th class="py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-surface-container-low">
                            @forelse($riwayatDistribusi as $distribusi)
                                <tr>
                                    <td class="py-4">{{ $distribusi->tanggal ? $distribusi->tanggal->format('d M Y') : '-' }}</td>
                                    <td class="py-4">{{ $distribusi->menuHarian->nama ?? '-' }}</td>
                                    <td class="py-4">{{ $distribusi->sppg->nama ?? '-' }}</td>
                                    <td class="py-4">{{ number_format($distribusi->jumlah_porsi, 0, ',', '.') }}</td>
                                    <td class="py-4">
                                        @php $laporanSekolah = $distribusi->laporanSekolahs->sortByDesc('id')->first(); @endphp
                                        @if($laporanSekolah)
                                            <span class="px-3 py-1 rounded-full bg-green-100 text-green-800 text-xs font-bold">Sudah Lapor</span>
                                        @else
                                            <span class="text-on-surface-variant">-</span>
                                        @endif
                                    </td>
                                    <td class="py-4">
                                        @if((int) $distribusi->status_distribusi > 1)
                                            <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-800 text-xs font-bold">Sudah Lapor Sekolah</span>
                                        @else
                                            <span class="px-3 py-1 rounded-full bg-yellow-100 text-yellow-800 text-xs font-bold">Belum Lapor Sekolah</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-5 text-center text-on-surface-variant">Riwayat distribusi SPPG belum tersedia.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($riwayatDistribusi->hasPages())
                    <div class="mt-5 flex items-center justify-between text-sm">
                        <span class="text-on-surface-variant">Halaman {{ $riwayatDistribusi->currentPage() }} dari {{ $riwayatDistribusi->lastPage() }}</span>
                        <div class="flex gap-2">
                            @if($riwayatDistribusi->onFirstPage())
                                <span class="px-4 py-2 rounded-lg bg-surface-container text-on-surface-variant">Sebelumnya</span>
                            @else
                                <a href="{{ $riwayatDistribusi->previousPageUrl() }}" class="px-4 py-2 rounded-lg bg-primary text-white font-bold">Sebelumnya</a>
                            @endif
                            @if($riwayatDistribusi->hasMorePages())
                                <a href="{{ $riwayatDistribusi->nextPageUrl() }}" class="px-4 py-2 rounded-lg bg-primary text-white font-bold">Berikutnya</a>
                            @else
                                <span class="px-4 py-2 rounded-lg bg-surface-container text-on-surface-variant">Berikutnya</span>
                            @endif
                        </div>
                    </div>
                @endif
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-xs uppercase tracking-widest text-on-surface-variant">
                                <th class="py-3">Tanggal</th>
                                <th class="py-3">SPPG</th>
                                <th class="py-3">Menu</th>
                                <th class="py-3">Rating</th>
                                <th class="py-3">Foto</th>
                                <th class="py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-surface-container-low">
                            @forelse($riwayatLaporanSekolah as $laporan)
                                <tr>
                                    <td class="py-4">{{ $laporan->tanggal ? $laporan->tanggal->format('d M Y') : '-' }}</td>
                                    <td class="py-4">{{ $laporan->sppg->nama ?? '-' }}</td>
                                    <td class="py-4">{{ $laporan->menuHarian->nama ?? 'Laporan menu sekolah' }}</td>
                                    <td class="py-4">
                                        @php $rating = (int) $laporan->rating; @endphp
                                        @if($rating)
                                            <span class="text-yellow-500 whitespace-nowrap">
                                                @for($i = 1; $i <= 5; $i++)
                                                    {!! $i <= $rating ? '&#9733;' : '&#9734;' !!}
                                                @endfor
                                            </span>
                                            <span class="text-on-surface-variant text-sm ml-1">({{ $rating }}/5)</span>
                                        @else
                                            <span class="text-on-surface-variant">-</span>
                                        @endif
                                    </td>
                                    <td class="py-4">
                                        @if($laporan->foto_menu || $laporan->foto_siswa)
                                            <div class="flex items-center gap-2">
                                                @if($laporan->foto_menu)
                                                    <a href="{{ asset('po-content/uploads/'.$laporan->foto_menu) }}" data-photo-popup data-photo-title="Foto menu - {{ $sekolah->nama }}" class="inline-flex items-center gap-2">
                                                        <img src="{{ asset('po-content/uploads/'.$laporan->foto_menu) }}" alt="Foto menu laporan sekolah" class="w-16 h-12 object-cover rounded-lg border">
                                                    </a>
                                                @endif
                                                @if($laporan->foto_siswa)
                                                    <a href="{{ asset('po-content/uploads/'.$laporan->foto_siswa) }}" data-photo-popup data-photo-title="Foto siswa - {{ $sekolah->nama }}" class="inline-flex items-center gap-2">
                                                        <img src="{{ asset('po-content/uploads/'.$laporan->foto_siswa) }}" alt="Foto siswa makan" class="w-16 h-12 object-cover rounded-lg border">
                                                    </a>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-on-surface-variant">-</span>
                                        @endif
                                    </td>
                                    <td class="py-4">
                                        @if($laporan->status_laporan == 2)
                                            <span class="px-3 py-1 rounded-full bg-green-100 text-green-800 text-xs font-bold">Terverifikasi</span>
                                        @elseif($laporan->status_laporan == 3)
                                            <span class="px-3 py-1 rounded-full bg-red-100 text-red-800 text-xs font-bold">Perlu Perbaikan</span>
                                        @else
                                            <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-800 text-xs font-bold">Sudah Lapor</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-5 text-center text-on-surface-variant">Riwayat laporan sekolah belum tersedia.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if(method_exists($riwayatLaporanSekolah, 'hasPages') && $riwayatLaporanSekolah->hasPages())
                    <div class="mt-5 flex items-center justify-between text-sm">
                        <span class="text-on-surface-variant">Halaman {{ $riwayatLaporanSekolah->currentPage() }} dari {{ $riwayatLaporanSekolah->lastPage() }}</span>
                        <div class="flex gap-2">
                            @if($riwayatLaporanSekolah->onFirstPage())
                                <span class="px-4 py-2 rounded-lg bg-surface-container text-on-surface-variant">Sebelumnya</span>
                            @else
                                <a href="{{ $riwayatLaporanSekolah->previousPageUrl() }}" class="px-4 py-2 rounded-lg bg-primary text-white font-bold">Sebelumnya</a>
                            @endif
                            @if($riwayatLaporanSekolah->hasMorePages())
                                <a href="{{ $riwayatLaporanSekolah->nextPageUrl() }}" class="px-4 py-2 rounded-lg bg-primary text-white font-bold">Berikutnya</a>
                            @else
                                <span class="px-4 py-2 rounded-lg bg-surface-container text-on-surface-variant">Berikutnya</span>
                            @endif
                        </div>
                    </div>
                @endif
            @endif
        </section>
        <!-- Map Integration -->
        <section class="bg-surface-container-lowest rounded-[2rem] p-8 shadow-[0px_12px_32px_rgba(24,28,30,0.06)] space-y-6">
            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-6">
                <div>
                    <h3 class="text-2xl font-bold font-headline text-primary">Lokasi Sekolah</h3>
                    <p class="text-on-surface-variant mt-2">{{ $sekolah->alamat ?: 'Alamat sekolah belum tersedia' }}</p>
                    @if($sekolah->wilayah)
                        <p class="text-sm text-on-surface-variant mt-1">
                            {{ $sekolah->wilayah->nama_wilayah }}
                            @if($sekolah->wilayah->parent)
                                , {{ $sekolah->wilayah->parent->nama_wilayah }}
                            @endif
                        </p>
                    @endif
                </div>
                <div class="flex flex-col gap-3 md:items-end">
                    <div class="bg-primary-fixed text-on-primary-fixed px-4 py-3 rounded-2xl text-sm font-bold">
                        @if($hasSekolahCoordinate)
                            {{ $sekolah->latitude }}, {{ $sekolah->longitude }}
                        @else
                            Koordinat belum tersedia
                        @endif
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <span class="flex items-center gap-1 text-xs font-medium text-on-surface-variant bg-surface-container px-3 py-1 rounded-full">
                            <span class="w-2 h-2 rounded-full bg-primary"></span> Sekolah
                        </span>
                        @if($hasSppgCoordinate)
                            <span class="flex items-center gap-1 text-xs font-medium text-on-surface-variant bg-surface-container px-3 py-1 rounded-full">
                                <span class="w-2 h-2 rounded-full bg-secondary"></span> Dapur SPPG
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="h-96 rounded-[2rem] overflow-hidden shadow-inner relative group border-4 border-surface-container-low bg-surface-container-low">
                @if($hasSekolahCoordinate)
                    <div id="sekolah-map"></div>
                @else
                    <div class="h-full flex flex-col items-center justify-center text-on-surface-variant gap-2">
                        <span class="material-symbols-outlined text-6xl">location_off</span>
                        <p class="font-bold">Lokasi belum dapat ditampilkan</p>
                        <p class="text-sm">Latitude dan longitude sekolah belum diisi.</p>
                    </div>
                @endif
            </div>
        </section>
    </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('click', function (event) {
    const link = event.target.closest('[data-photo-popup]');
    if (!link) {
        return;
    }

    event.preventDefault();
    openSekolahPhotoPopup(link.href, link.dataset.photoTitle || 'Foto laporan');
});

function openSekolahPhotoPopup(src, title) {
    let modal = document.getElementById('sekolah-photo-popup');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'sekolah-photo-popup';
        modal.className = 'fixed inset-0 z-[9999] hidden items-center justify-center bg-black/70 p-4';
        modal.innerHTML = `
            <div class="relative w-full max-w-4xl rounded-2xl bg-white shadow-2xl overflow-hidden">
                <div class="flex items-center justify-between gap-4 px-5 py-4 border-b border-surface-container-low">
                    <h3 class="font-bold text-primary" data-popup-title>Foto laporan</h3>
                    <button type="button" class="rounded-full bg-surface-container-low text-primary w-10 h-10 inline-flex items-center justify-center" data-popup-close>
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                <div class="bg-surface-container-low p-4">
                    <img src="" alt="Foto laporan" class="max-h-[75vh] w-full object-contain rounded-xl" data-popup-image>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
        modal.addEventListener('click', function (event) {
            if (event.target === modal || event.target.closest('[data-popup-close]')) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        });
    }

    modal.querySelector('[data-popup-title]').textContent = title;
    modal.querySelector('[data-popup-image]').src = src;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}
</script>
@if($hasSekolahCoordinate)
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sekolahLat = {{ (float) $sekolah->latitude }};
        const sekolahLng = {{ (float) $sekolah->longitude }};
        const map = L.map('sekolah-map').setView([sekolahLat, sekolahLng], 15);
        const bounds = [[sekolahLat, sekolahLng]];

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        const sekolahMarker = L.marker([sekolahLat, sekolahLng])
            .addTo(map)
            .bindPopup(`<strong>{{ addslashes($sekolah->nama) }}</strong><br>{{ addslashes($sekolah->alamat ?: '-') }}`);
        sekolahMarker.openPopup();

        @if($hasSppgCoordinate)
            const sppgLat = {{ (float) $sppgUtama->latitude }};
            const sppgLng = {{ (float) $sppgUtama->longitude }};
            L.marker([sppgLat, sppgLng])
                .addTo(map)
                .bindPopup(`<strong>{{ addslashes($sppgUtama->nama) }}</strong><br>Dapur SPPG`);
            bounds.push([sppgLat, sppgLng]);
        @endif

        if (bounds.length > 1) {
            map.fitBounds(bounds, { padding: [40, 40] });
        }

        setTimeout(function () {
            map.invalidateSize();
        }, 150);
    });
</script>
@endif
@endpush
