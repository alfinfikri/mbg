@extends(getTheme('layouts.app'))

@section('content')
@php
    $status = [
        1 => ['Operasional Aktif', 'bg-secondary-fixed'],
        2 => ['Tidak Aktif', 'bg-yellow-300'],
        3 => ['Belum Operasi', 'bg-red-300'],
    ][$sppg->status_layanan] ?? ['Status Belum Diisi', 'bg-surface-container'];
    $fotoDapur = $sppg->foto_dapur ? asset('po-content/uploads/'.$sppg->foto_dapur) : null;
    $menuFoto = $menuTerbaru && $menuTerbaru->foto ? asset('po-content/uploads/'.$menuTerbaru->foto) : null;
    $hasSppgCoordinate = !empty($sppg->latitude) && !empty($sppg->longitude);
    $resetRiwayatUrl = url()->current();
@endphp
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
    #sppg-map {
        height: 24rem;
        width: 100%;
    }
</style>

<section class="pt-36 pb-12 max-w-7xl mx-auto px-6">
    <section class="relative overflow-hidden rounded-[2rem] bg-primary-container mb-12 shadow-2xl">
        <div class="grid md:grid-cols-2 items-stretch min-h-[400px]">
            <div class="p-10 md:p-16 flex flex-col justify-center relative z-10 text-white">
                <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-md px-4 py-1.5 rounded-full mb-6 w-fit">
                    <span class="w-2 h-2 {{ $status[1] }} rounded-full animate-pulse"></span>
                    <span class="text-xs font-semibold uppercase tracking-widest">{{ $status[0] }}</span>
                </div>
                <h1 class="text-5xl md:text-6xl font-bold mb-4 tracking-tight leading-tight">{{ $sppg->nama }}</h1>
                <p class="text-blue-100 text-lg max-w-md mb-8 flex items-start gap-2">
                    <span class="material-symbols-outlined mt-1">location_on</span>
                    {{ $sppg->alamat ?: '-' }}
                    @if($sppg->wilayah)
                        <br>{{ $sppg->wilayah->nama_wilayah }}
                        @if($sppg->wilayah->parent)
                            , {{ $sppg->wilayah->parent->nama_wilayah }}
                        @endif
                    @endif
                </p>
                <div class="flex flex-wrap gap-4">
                    @if($sppg->no_hp)
                        <a href="tel:{{ $sppg->no_hp }}" class="bg-surface-container-lowest text-primary font-bold px-8 py-4 rounded-xl shadow-lg hover:bg-blue-50 transition-all active:scale-95">
                            Hubungi Unit
                        </a>
                    @endif
                    @if($sppg->slhs_file)
                        <a href="{{ asset('po-content/uploads/'.$sppg->slhs_file) }}" target="_blank" class="bg-transparent border border-white/30 backdrop-blur-sm text-white font-semibold px-8 py-4 rounded-xl hover:bg-white/10 transition-all">
                            Lihat SLHS
                        </a>
                    @endif
                    @if($sppg->halal_file)
                        <a href="{{ asset('po-content/uploads/'.$sppg->halal_file) }}" target="_blank" class="bg-transparent border border-white/30 backdrop-blur-sm text-white font-semibold px-8 py-4 rounded-xl hover:bg-white/10 transition-all">
                            Lihat Sertifikat Halal
                        </a>
                    @endif
                </div>
            </div>
            <div class="relative h-64 md:h-full overflow-hidden">
                @if($fotoDapur)
                    <img alt="Dapur SPPG" class="absolute inset-0 w-full h-full object-cover" src="{{ $fotoDapur }}" />
                @else
                    <div class="absolute inset-0 bg-primary/30 flex items-center justify-center">
                        <span class="material-symbols-outlined text-white text-7xl">soup_kitchen</span>
                    </div>
                @endif
                <div class="absolute inset-0 bg-gradient-to-r from-primary-container/80 to-transparent md:block hidden"></div>
            </div>
        </div>
    </section>

    <div class="grid grid-cols-1 md:grid-cols-12 gap-8 mb-12">
        <div class="md:col-span-8 bg-surface-container-lowest rounded-[2rem] p-8 shadow-[0_12px_32px_rgba(24,28,30,0.06)] relative overflow-hidden">
            <div class="absolute top-0 left-0 w-1.5 h-full bg-tertiary-fixed"></div>
            <h2 class="text-2xl font-bold mb-8 text-primary">Profil SPPG</h2>
            <div class="grid sm:grid-cols-3 gap-8">
                <div class="flex flex-col gap-4">
                    <div class="bg-surface-container-low w-16 h-16 rounded-2xl flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined text-3xl">supervisor_account</span>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-outline uppercase tracking-wider mb-1">Penanggung Jawab</p>
                        <p class="font-bold text-on-surface">{{ $sppg->nama_penanggung_jawab ?: '-' }}</p>
                    </div>
                </div>
                <div class="flex flex-col gap-4">
                    <div class="bg-surface-container-low w-16 h-16 rounded-2xl flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined text-3xl">restaurant_menu</span>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-outline uppercase tracking-wider mb-1">Ahli Gizi</p>
                        <p class="font-bold text-on-surface">{{ $sppg->nama_ahli_gizi ?: '-' }}</p>
                    </div>
                </div>
                <div class="flex flex-col gap-4">
                    <div class="bg-surface-container-low w-16 h-16 rounded-2xl flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined text-3xl">groups</span>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-outline uppercase tracking-wider mb-1">Jumlah Petugas</p>
                        <p class="font-bold text-on-surface">{{ number_format($sppg->jumlah_petugas, 0, ',', '.') }} Petugas</p>
                    </div>
                </div>
            </div>

            <div class="mt-12 pt-8 border-t border-surface-container">
                <div class="grid md:grid-cols-3 gap-4">
                    <div class="bg-primary/5 rounded-2xl p-6">
                        <p class="text-xs font-medium text-outline uppercase tracking-wider">Kapasitas Produksi</p>
                        <p class="text-3xl font-extrabold text-primary mt-2">{{ number_format($sppg->kapasitas_produksi, 0, ',', '.') }}</p>
                        <p class="text-sm text-on-surface-variant">porsi/hari</p>
                    </div>
                    <div class="bg-secondary-container/20 rounded-2xl p-6">
                        <p class="text-xs font-medium text-outline uppercase tracking-wider">Total Sekolah</p>
                        <p class="text-3xl font-extrabold text-primary mt-2">{{ number_format($totalSekolah, 0, ',', '.') }}</p>
                        <p class="text-sm text-on-surface-variant">sekolah dilayani</p>
                    </div>
                    <div class="bg-tertiary-fixed/20 rounded-2xl p-6">
                        <p class="text-xs font-medium text-outline uppercase tracking-wider">Total Penerima</p>
                        <p class="text-3xl font-extrabold text-primary mt-2">{{ number_format($totalPenerima, 0, ',', '.') }}</p>
                        <p class="text-sm text-on-surface-variant">penerima manfaat</p>
                    </div>
                </div>
            </div>

            <div class="mt-12 pt-8 border-t border-surface-container">
                <div class="flex flex-col lg:flex-row gap-6">
                    <div class="lg:w-2/5">
                        @if($menuFoto)
                            <img src="{{ $menuFoto }}" alt="{{ $menuTerbaru->nama }}" class="w-full h-52 object-cover rounded-2xl">
                        @else
                            <div class="w-full h-52 rounded-2xl bg-surface-container-low flex items-center justify-center text-on-surface-variant">
                                <span class="material-symbols-outlined text-6xl">restaurant</span>
                            </div>
                        @endif
                    </div>
                    <div class="lg:w-3/5">
                        <p class="text-xs font-medium text-outline uppercase tracking-wider mb-2">Menu Harian Hari Ini</p>
                        @if($menuTerbaru)
                            <p class="text-sm text-on-surface-variant">{{ $menuTerbaru->tanggal ? $menuTerbaru->tanggal->format('d M Y') : '-' }}</p>
                            <h3 class="text-2xl font-extrabold text-primary mt-1">{{ $menuTerbaru->nama }}</h3>
                            <p class="text-sm text-on-surface-variant mt-3 leading-relaxed">{{ $menuTerbaru->deskripsi ?: '-' }}</p>
                        @else
                            <h3 class="text-xl font-bold text-primary">Menu hari ini belum tersedia</h3>
                            <p class="text-sm text-on-surface-variant mt-2">Menu harian untuk hari ini belum tersedia.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="md:col-span-4 flex flex-col gap-8">
            <div class="bg-surface-container-lowest rounded-[2rem] p-8 shadow-[0_12px_32px_rgba(24,28,30,0.06)] flex-1">
                <h2 class="text-xl font-bold mb-6 text-on-surface">Sertifikasi & Kontak</h2>
                <div class="space-y-4">
                    <div class="p-4 rounded-2xl bg-secondary-container/20 border border-secondary/10">
                        <p class="text-xs text-on-secondary-container uppercase font-bold">SLHS</p>
                        <p class="font-bold text-on-secondary-fixed">{{ $sppg->slhs_nomor ?: '-' }}</p>
                        <p class="text-xs text-on-secondary-container">Terbit: {{ $sppg->slhs_tanggal_terbit ?: $sppg->slhs_tanggal ?: '-' }}</p>
                        <p class="text-xs text-on-secondary-container">Berlaku hingga: {{ $sppg->slhs_berlaku_hingga ?: '-' }}</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-tertiary-fixed/30 border border-tertiary/10">
                        <p class="text-xs text-tertiary uppercase font-bold">Sertifikat Halal</p>
                        <p class="font-bold text-tertiary">{{ $sppg->halal_nomor ?: '-' }}</p>
                        <p class="text-xs text-on-surface-variant">Terbit: {{ $sppg->halal_tanggal_terbit ?: '-' }}</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-primary/5 border border-primary/10">
                        <p class="text-xs text-primary uppercase font-bold">Kontak</p>
                        <p class="font-bold text-primary">{{ $sppg->no_hp ?: '-' }}</p>
                        <p class="text-xs text-on-surface-variant">{{ $sppg->email ?: '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="bg-surface-container-lowest rounded-[2rem] p-8 shadow-[0_12px_32px_rgba(24,28,30,0.06)] mb-12">
        <h2 class="text-2xl font-bold text-primary mb-6">Ringkasan Laporan dan Distribusi Hari Ini</h2>
        <div class="grid md:grid-cols-3 gap-4">
            <div class="bg-surface-container-low rounded-2xl p-6">
                <p class="text-xs font-medium text-outline uppercase tracking-wider">Distribusi Hari Ini</p>
                <p class="text-3xl font-extrabold text-primary mt-2">{{ number_format($ringkasanDistribusi['hari_ini'], 0, ',', '.') }}</p>
                <p class="text-sm text-on-surface-variant">{{ number_format($ringkasanDistribusi['porsi_hari_ini'], 0, ',', '.') }} porsi</p>
            </div>
            <div class="bg-surface-container-low rounded-2xl p-6">
                <p class="text-xs font-medium text-outline uppercase tracking-wider">Laporan Sekolah Hari Ini</p>
                <p class="text-3xl font-extrabold text-primary mt-2">{{ number_format($ringkasanLaporanSekolah['hari_ini'], 0, ',', '.') }}</p>
                <p class="text-sm text-on-surface-variant">total {{ number_format($ringkasanLaporanSekolah['total'], 0, ',', '.') }} laporan hari ini</p>
            </div>
            <div class="bg-surface-container-low rounded-2xl p-6">
                <p class="text-xs font-medium text-outline uppercase tracking-wider">Belum Lapor Sekolah</p>
                <p class="text-3xl font-extrabold text-primary mt-2">{{ number_format($ringkasanDistribusi['belum_lapor'], 0, ',', '.') }}</p>
                <p class="text-sm text-on-surface-variant">dari {{ number_format($ringkasanDistribusi['total'], 0, ',', '.') }} distribusi hari ini</p>
            </div>
        </div>
    </section>

    <section class="bg-surface-container-lowest rounded-[2rem] p-8 shadow-[0_12px_32px_rgba(24,28,30,0.06)] mb-12">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <div>
                <h2 class="text-2xl font-bold text-primary">Riwayat Distribusi</h2>
                <p class="text-on-surface-variant">Distribusi MBG dari {{ $sppg->nama }} ke sekolah yang dilayani.</p>
            </div>
            <form method="GET" action="{{ url()->current() }}" class="flex flex-col gap-2 sm:flex-row sm:items-end">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-1">Filter Tanggal</label>
                    <input type="date" name="riwayat_tanggal" value="{{ $riwayatTanggal }}" class="h-10 rounded-lg border border-outline-variant bg-white px-3 text-sm">
                </div>
                <button type="submit" class="h-10 px-4 rounded-lg bg-primary text-white text-sm font-bold">Filter</button>
                <a href="{{ $resetRiwayatUrl }}" class="h-10 px-4 rounded-lg bg-surface-container text-primary text-sm font-bold inline-flex items-center">Reset</a>
            </form>
        </div>

        <div class="mb-6 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <p class="text-sm text-on-surface-variant">
                Menampilkan riwayat tanggal {{ \Carbon\Carbon::parse($riwayatTanggal)->format('d M Y') }}.
            </p>
            <div class="bg-surface-container-low text-primary px-4 py-2 rounded-xl flex items-center gap-2 w-fit">
                <span class="material-symbols-outlined">package</span>
                <span class="font-bold">{{ number_format($riwayatDistribusi->total(), 0, ',', '.') }} Data</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-low text-on-surface-variant uppercase text-[10px] tracking-widest font-bold">
                        <th class="px-5 py-4">Tanggal</th>
                        <th class="px-5 py-4">Sekolah</th>
                        <th class="px-5 py-4">Menu</th>
                        <th class="px-5 py-4">Porsi</th>
                        <th class="px-5 py-4">Laporan</th>
                        <th class="px-5 py-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-container-low">
                    @forelse($riwayatDistribusi as $distribusi)
                        @php
                            $statusDistribusi = [
                                1 => ['Belum Lapor Sekolah', 'bg-yellow-100 text-yellow-800'],
                                2 => ['Sudah Lapor Sekolah', 'bg-blue-100 text-blue-800'],
                            ][(int) $distribusi->status_distribusi > 1 ? 2 : 1];
                        @endphp
                        <tr class="hover:bg-surface-container-low/50 transition-colors">
                            <td class="px-5 py-4 whitespace-nowrap font-semibold text-on-surface">
                                {{ $distribusi->tanggal ? $distribusi->tanggal->format('d M Y') : '-' }}
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex flex-col">
                                    <span class="font-bold text-on-surface">{{ $distribusi->sekolah->nama ?? '-' }}</span>
                                    <span class="text-xs text-on-surface-variant">{{ optional(optional($distribusi->sekolah)->wilayah)->nama_wilayah ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-on-surface-variant">
                                {{ $distribusi->menuHarian->nama ?? '-' }}
                            </td>
                            <td class="px-5 py-4 font-bold text-primary">
                                {{ number_format($distribusi->jumlah_porsi, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-4">
                                <span class="px-3 py-1 rounded-full bg-primary-fixed text-on-primary-fixed text-xs font-bold">
                                    {{ number_format($hasLaporanSekolahTable ? $distribusi->laporanSekolahs->count() : 0, 0, ',', '.') }} Laporan
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="px-3 py-1 rounded-full {{ $statusDistribusi[1] }} text-xs font-bold">{{ $statusDistribusi[0] }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center p-6 text-on-surface-variant">
                                Riwayat distribusi belum tersedia untuk tanggal ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($riwayatDistribusi->hasPages())
            <div class="mt-6 flex flex-col md:flex-row md:items-center justify-between gap-4 text-sm">
                <span class="text-on-surface-variant">
                    Menampilkan {{ $riwayatDistribusi->firstItem() }} - {{ $riwayatDistribusi->lastItem() }} dari {{ $riwayatDistribusi->total() }} distribusi
                </span>
                <div class="flex items-center gap-2">
                    @if($riwayatDistribusi->onFirstPage())
                        <span class="px-4 py-2 rounded-xl bg-surface-container text-on-surface-variant">Sebelumnya</span>
                    @else
                        <a href="{{ $riwayatDistribusi->previousPageUrl() }}" class="px-4 py-2 rounded-xl bg-primary text-white font-bold">Sebelumnya</a>
                    @endif

                    @if($riwayatDistribusi->hasMorePages())
                        <a href="{{ $riwayatDistribusi->nextPageUrl() }}" class="px-4 py-2 rounded-xl bg-primary text-white font-bold">Berikutnya</a>
                    @else
                        <span class="px-4 py-2 rounded-xl bg-surface-container text-on-surface-variant">Berikutnya</span>
                    @endif
                </div>
            </div>
        @endif
    </section>

    <section class="bg-surface-container-lowest rounded-[2rem] p-10 shadow-[0_12px_32px_rgba(24,28,30,0.06)] mb-12">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-10">
            <div>
                <h2 class="text-2xl font-bold text-primary">Sekolah yang Dilayani</h2>
                <p class="text-on-surface-variant">Daftar sekolah penerima manfaat dari {{ $sppg->nama }}</p>
            </div>
            <div class="bg-tertiary-fixed text-on-tertiary-fixed px-4 py-2 rounded-xl flex items-center gap-2">
                <span class="material-symbols-outlined">school</span>
                <span class="font-bold">{{ number_format($totalSekolah, 0, ',', '.') }} Sekolah</span>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($sppg->sekolahs as $sekolah)
                <div class="bg-surface-container-low border border-outline-variant/30 p-6 rounded-2xl hover:border-primary/30 hover:shadow-lg transition-all">
                    <div class="flex items-start justify-between gap-4 mb-5">
                        <div>
                            <p class="text-xs font-bold text-primary mb-2 uppercase tracking-widest">
                                {{ optional(optional($sekolah->wilayah)->parent)->nama_wilayah ?? 'Wilayah' }}
                            </p>
                            <h4 class="font-bold text-lg text-on-surface leading-snug">{{ $sekolah->nama }}</h4>
                        </div>
                        <span class="material-symbols-outlined text-primary bg-primary-fixed p-2 rounded-xl">school</span>
                    </div>

                    <p class="text-sm text-on-surface-variant leading-relaxed min-h-[44px]">
                        {{ \Illuminate\Support\Str::limit($sekolah->alamat ?: 'Alamat sekolah belum tersedia', 88) }}
                    </p>

                    <div class="grid grid-cols-2 gap-3 my-5">
                        <div class="bg-surface-container-lowest rounded-xl p-3">
                            <p class="text-[10px] uppercase tracking-widest text-outline font-bold">Penerima</p>
                            <p class="text-xl font-extrabold text-primary mt-1">{{ number_format($sekolah->jumlah_total, 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-surface-container-lowest rounded-xl p-3">
                            <p class="text-[10px] uppercase tracking-widest text-outline font-bold">Wilayah</p>
                            <p class="text-sm font-bold text-on-surface mt-1">{{ optional($sekolah->wilayah)->nama_wilayah ?? '-' }}</p>
                        </div>
                    </div>

                    <a href="{{ route('sekolah.detail', Hashids::encode($sekolah->id)) }}" class="w-full inline-flex items-center justify-center gap-2 py-2.5 rounded-xl bg-primary text-white font-bold text-sm hover:bg-primary-container transition-colors">
                        Detail Sekolah
                        <span class="material-symbols-outlined text-base">arrow_forward</span>
                    </a>
                </div>
            @empty
                <div class="col-span-full text-center text-on-surface-variant py-8">
                    Belum ada sekolah yang terhubung dengan SPPG ini.
                </div>
            @endforelse
        </div>
    </section>

    <section class="bg-surface-container-lowest rounded-[2rem] p-8 shadow-[0_12px_32px_rgba(24,28,30,0.06)] mb-12">
        <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-6 mb-6">
            <div>
                <h2 class="text-2xl font-bold text-primary">Lokasi SPPG</h2>
                <p class="text-on-surface-variant mt-2">{{ $sppg->alamat ?: 'Alamat SPPG belum tersedia' }}</p>
                @if($sppg->wilayah)
                    <p class="text-sm text-on-surface-variant mt-1">
                        {{ $sppg->wilayah->nama_wilayah }}
                        @if($sppg->wilayah->parent)
                            , {{ $sppg->wilayah->parent->nama_wilayah }}
                        @endif
                    </p>
                @endif
            </div>
            <div class="bg-primary-fixed text-on-primary-fixed px-4 py-3 rounded-2xl text-sm font-bold">
                @if($hasSppgCoordinate)
                    {{ $sppg->latitude }}, {{ $sppg->longitude }}
                @else
                    Koordinat belum tersedia
                @endif
            </div>
        </div>

        <div class="h-96 rounded-[2rem] overflow-hidden shadow-inner relative border-4 border-surface-container-low bg-surface-container-low">
            @if($hasSppgCoordinate)
                <div id="sppg-map"></div>
            @else
                <div class="h-full flex flex-col items-center justify-center text-on-surface-variant gap-2">
                    <span class="material-symbols-outlined text-6xl">location_off</span>
                    <p class="font-bold">Lokasi belum dapat ditampilkan</p>
                    <p class="text-sm">Latitude dan longitude SPPG belum diisi.</p>
                </div>
            @endif
        </div>
    </section>

</section>
@endsection

@push('scripts')
@if($hasSppgCoordinate)
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sppgLat = {{ (float) $sppg->latitude }};
        const sppgLng = {{ (float) $sppg->longitude }};
        const map = L.map('sppg-map').setView([sppgLat, sppgLng], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        L.marker([sppgLat, sppgLng])
            .addTo(map)
            .bindPopup(`<strong>{{ addslashes($sppg->nama) }}</strong><br>{{ addslashes($sppg->alamat ?: '-') }}`)
            .openPopup();
    });
</script>
@endif
@endpush
