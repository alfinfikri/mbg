@extends(getTheme('layouts.app'))

@section('content')
@php
    $imageUrl = function ($path, $user = null) {
        if (!$path) {
            return null;
        }

        if (\Illuminate\Support\Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        if (\Illuminate\Support\Str::startsWith($path, ['storage/', 'po-content/'])) {
            return asset($path);
        }

        if (file_exists(public_path('storage/'.$path))) {
            return asset('storage/'.$path);
        }

        return getPicture($path, '', $user);
    };

    $educationCards = [
        ['icon' => 'breakfast_dining', 'title' => 'Pentingnya sarapan bergizi', 'body' => 'Asupan seimbang membantu anak lebih siap belajar, berkonsentrasi, dan beraktivitas sepanjang hari.'],
        ['icon' => 'fact_check', 'title' => 'Peran sekolah dalam monitoring', 'body' => 'Sekolah membantu memastikan makanan diterima, terdokumentasi, dan dilaporkan sebagai bukti layanan harian.'],
        ['icon' => 'support_agent', 'title' => 'Cara menyampaikan pengaduan', 'body' => 'Masyarakat dapat mengirim keluhan atau masukan melalui kanal pengaduan agar bisa ditindaklanjuti.'],
    ];

    $sppgProgress = min((float) ($laporanProgram['persen_laporan_sppg'] ?? 0), 100);
    $sekolahProgress = min((float) ($laporanProgram['persen_laporan_sekolah'] ?? 0), 100);
    $formatPercent = function ($value) {
        return rtrim(rtrim(number_format((float) $value, 1, ',', '.'), '0'), ',');
    };
@endphp

<section class="pt-36 pb-20 px-6 bg-surface">
    <div class="max-w-7xl mx-auto grid lg:grid-cols-12 gap-10 items-center">
        <div class="lg:col-span-7">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-secondary-container text-on-secondary-container font-bold text-sm mb-6">
                <span class="w-2 h-2 rounded-full bg-secondary"></span>
                Program Nasional Pemenuhan Gizi
            </div>
            <h1 class="text-5xl md:text-6xl font-bold font-headline leading-tight text-primary mb-5">Makan Bergizi Gratis</h1>
            <h2 class="text-2xl md:text-3xl font-bold text-on-surface mb-5">Monitoring dan Informasi Program MBG</h2>
            <p class="text-lg text-on-surface-variant max-w-2xl leading-relaxed mb-8">
                Portal publik untuk melihat perkembangan layanan MBG, mulai dari data sekolah dan SPPG, menu harian, distribusi makanan, laporan sekolah, hingga pengaduan masyarakat.
            </p>
            <div class="flex flex-wrap gap-3">
                <a href="{{ url('/sekolah') }}" class="px-5 py-3 rounded-xl bg-primary text-white font-bold hover:bg-primary-container transition-colors">Lihat Daftar Sekolah</a>
                <a href="{{ url('/sppg') }}" class="px-5 py-3 rounded-xl bg-secondary text-white font-bold hover:bg-secondary/90 transition-colors">Lihat SPPG</a>
                <a href="{{ url('/contact') }}" class="px-5 py-3 rounded-xl border border-outline-variant bg-surface-container-low text-primary font-bold hover:bg-primary-fixed transition-colors">Ajukan Pengaduan</a>
                <a href="{{ url('/tracking') }}" class="px-5 py-3 rounded-xl border border-outline-variant text-primary font-bold hover:bg-surface-container-low transition-colors">Lacak Pengaduan</a>
            </div>
        </div>
        <div class="lg:col-span-5">
            <div class="bg-primary rounded-[2rem] p-8 text-white shadow-2xl">
                <p class="text-sm uppercase tracking-widest text-primary-fixed font-bold mb-4">Ringkasan Hari Ini</p>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white/10 rounded-2xl p-5">
                        <p class="text-3xl font-extrabold">{{ number_format($ringkasanHariIni['total_distribusi'], 0, ',', '.') }}</p>
                        <p class="text-sm text-primary-fixed mt-1">Distribusi</p>
                    </div>
                    <div class="bg-white/10 rounded-2xl p-5">
                        <p class="text-3xl font-extrabold">{{ number_format($ringkasanHariIni['total_laporan'], 0, ',', '.') }}</p>
                        <p class="text-sm text-primary-fixed mt-1">Laporan Sekolah</p>
                    </div>
                    <div class="col-span-2 bg-white rounded-2xl p-5 text-primary">
                        <p class="text-sm font-bold text-on-surface-variant">Porsi tercatat hari ini</p>
                        <p class="text-4xl font-extrabold mt-1">{{ number_format($ringkasanHariIni['total_porsi'], 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-14 px-6 bg-surface-container-low">
    <div class="max-w-7xl mx-auto grid grid-cols-2 lg:grid-cols-6 gap-4">
        <div class="bg-white p-5 rounded-2xl shadow-sm">
            <span class="material-symbols-outlined text-primary mb-3">school</span>
            <p class="text-3xl font-extrabold text-primary">{{ number_format($stats['total_sekolah'], 0, ',', '.') }}</p>
            <p class="text-sm text-on-surface-variant">Total Sekolah</p>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm">
            <span class="material-symbols-outlined text-secondary mb-3">soup_kitchen</span>
            <p class="text-3xl font-extrabold text-secondary">{{ number_format($stats['total_sppg'], 0, ',', '.') }}</p>
            <p class="text-sm text-on-surface-variant">Total SPPG</p>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm">
            <span class="material-symbols-outlined text-tertiary mb-3">restaurant_menu</span>
            <p class="text-3xl font-extrabold text-tertiary">{{ number_format($stats['total_menu_harian'], 0, ',', '.') }}</p>
            <p class="text-sm text-on-surface-variant">Menu Harian</p>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm">
            <span class="material-symbols-outlined text-primary mb-3">assignment_turned_in</span>
            <p class="text-3xl font-extrabold text-primary">{{ number_format($ringkasanHariIni['total_laporan'], 0, ',', '.') }}</p>
            <p class="text-sm text-on-surface-variant">Laporan Hari Ini</p>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm">
            <span class="material-symbols-outlined text-secondary mb-3">verified</span>
            <p class="text-3xl font-extrabold text-secondary">{{ number_format($ringkasanHariIni['terverifikasi'], 0, ',', '.') }}</p>
            <p class="text-sm text-on-surface-variant">Laporan Diverifikasi</p>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm">
            <span class="material-symbols-outlined text-tertiary mb-3">campaign</span>
            <p class="text-3xl font-extrabold text-tertiary">{{ number_format($stats['total_aduan'], 0, ',', '.') }}</p>
            <p class="text-sm text-on-surface-variant">Aduan Masuk</p>
        </div>
    </div>
</section>

<section class="py-20 px-6">
    <div class="max-w-7xl mx-auto grid lg:grid-cols-12 gap-10">
        <div class="lg:col-span-5">
            <p class="text-xs uppercase tracking-widest text-outline font-bold mb-3">Tentang Program</p>
            <h2 class="text-4xl font-bold font-headline text-primary leading-tight">Layanan gizi yang dipantau dari dapur, distribusi, hingga laporan sekolah.</h2>
        </div>
        <div class="lg:col-span-7 bg-white rounded-2xl p-8 shadow-sm">
            <p class="text-on-surface-variant leading-relaxed">
                MBG mendukung pemenuhan gizi peserta didik dan kelompok sasaran melalui makanan yang disiapkan oleh SPPG. Sekolah berperan sebagai penerima manfaat sekaligus pelapor bukti penerimaan, sehingga pelaksanaan program dapat dipantau secara terbuka.
            </p>
            <p class="text-on-surface-variant leading-relaxed mt-4">
                Informasi pada portal ini membantu masyarakat melihat data layanan, menu, distribusi, dan kanal pengaduan tanpa perlu masuk ke dashboard admin.
            </p>
        </div>
    </div>
</section>

<section class="pb-20 px-6">
    <div class="max-w-7xl mx-auto grid md:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-white rounded-2xl p-6 shadow-sm border-t-4 border-primary">
            <span class="material-symbols-outlined text-primary mb-4">flag</span>
            <h3 class="text-xl font-bold text-primary mb-2">Misi</h3>
            <p class="text-sm text-on-surface-variant leading-relaxed">Mendukung tumbuh kembang peserta didik melalui makanan bergizi yang aman, layak, dan tercatat.</p>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border-t-4 border-secondary">
            <span class="material-symbols-outlined text-secondary mb-4">track_changes</span>
            <h3 class="text-xl font-bold text-primary mb-2">Tujuan</h3>
            <p class="text-sm text-on-surface-variant leading-relaxed">Meningkatkan kesehatan, konsentrasi belajar, serta membangun kebiasaan makan sehat di lingkungan sekolah.</p>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border-t-4 border-tertiary">
            <span class="material-symbols-outlined text-tertiary mb-4">diversity_3</span>
            <h3 class="text-xl font-bold text-primary mb-2">Sasaran</h3>
            <p class="text-sm text-on-surface-variant leading-relaxed">Sekolah, peserta didik, dan kelompok penerima manfaat yang terhubung dengan unit layanan SPPG.</p>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border-t-4 border-primary-container">
            <span class="material-symbols-outlined text-primary-container mb-4">health_and_safety</span>
            <h3 class="text-xl font-bold text-primary mb-2">Manfaat</h3>
            <p class="text-sm text-on-surface-variant leading-relaxed">Pelaksanaan program lebih transparan, data lebih mudah dibandingkan, dan pengaduan lebih cepat ditindaklanjuti.</p>
        </div>
    </div>
</section>

<section class="py-20 px-6 bg-surface-container-low">
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-10">
            <div>
                <p class="text-xs uppercase tracking-widest text-outline font-bold mb-3">Menu Harian Terbaru</p>
                <h2 class="text-4xl font-bold font-headline text-primary">Menu & Gizi dari SPPG</h2>
            </div>
            <a href="{{ url('/menu') }}" class="inline-flex items-center gap-2 text-primary font-bold">Lihat Menu & Gizi <span class="material-symbols-outlined text-base">arrow_forward</span></a>
        </div>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($menuHarians as $menu)
                @php $menuFoto = $imageUrl($menu->foto, $menu->updated_by); @endphp
                <article class="bg-white rounded-2xl overflow-hidden shadow-sm">
                    <div class="h-48 bg-surface-container-low">
                        @if($menuFoto)
                            <img src="{{ $menuFoto }}" alt="{{ $menu->nama }}" class="w-full h-full object-cover">
                        @else
                            <div class="h-full flex items-center justify-center text-primary"><span class="material-symbols-outlined text-6xl">restaurant_menu</span></div>
                        @endif
                    </div>
                    <div class="p-6">
                        <p class="text-xs text-on-surface-variant">{{ $menu->tanggal ? $menu->tanggal->translatedFormat('d F Y') : '-' }}</p>
                        <h3 class="text-xl font-bold text-primary mt-1">{{ $menu->nama }}</h3>
                        <p class="text-sm text-on-surface-variant mt-1">{{ optional($menu->sppg)->nama ?? 'SPPG belum tersedia' }}</p>
                        <div class="grid grid-cols-2 gap-3 mt-5">
                            <div class="bg-surface-container-low rounded-xl p-3">
                                <p class="text-[10px] uppercase tracking-widest text-outline font-bold">Porsi Kecil</p>
                                <p class="font-bold text-primary mt-1">{{ number_format($menu->kecil_energi ?? 0, 0, ',', '.') }} kkal</p>
                            </div>
                            <div class="bg-surface-container-low rounded-xl p-3">
                                <p class="text-[10px] uppercase tracking-widest text-outline font-bold">Porsi Besar</p>
                                <p class="font-bold text-primary mt-1">{{ number_format($menu->besar_energi ?? 0, 0, ',', '.') }} kkal</p>
                            </div>
                        </div>
                        <a href="{{ url('/menu') }}" class="mt-5 inline-flex items-center gap-2 text-primary font-bold text-sm">Lihat detail <span class="material-symbols-outlined text-base">arrow_forward</span></a>
                    </div>
                </article>
            @empty
                <div class="col-span-full bg-white rounded-2xl p-8 text-center text-on-surface-variant">Menu harian belum tersedia.</div>
            @endforelse
        </div>
    </div>
</section>

<section class="report-section py-20 px-6">
    <div class="max-w-7xl mx-auto">
        <div class="max-w-3xl mb-10">
            <p class="text-xs uppercase tracking-widest text-outline font-bold mb-3">RINGKASAN LAPORAN HARI INI</p>
            <h2 class="text-4xl font-bold font-headline text-primary">Laporan Program MBG</h2>
            <p class="text-on-surface-variant mt-3 leading-relaxed">Pantau laporan harian SPPG dan sekolah secara ringkas.</p>
        </div>

        <div class="grid lg:grid-cols-2 gap-6">
            <article class="highlight-card min-h-[300px] rounded-[1.75rem] p-7 md:p-8 text-white shadow-xl bg-gradient-to-br from-[#b8371f] via-[#d65a2a] to-[#f2a23a] overflow-hidden relative">
                <div class="absolute -right-10 -top-10 w-40 h-40 rounded-full bg-white/10"></div>
                <div class="absolute right-8 bottom-8 w-24 h-24 rounded-full bg-white/10"></div>
                <div class="relative h-full flex flex-col">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined bg-white/20 rounded-2xl p-3 text-3xl">soup_kitchen</span>
                            <div>
                                <h3 class="text-2xl font-extrabold font-headline">Laporan SPPG Hari Ini</h3>
                                <p class="text-white/80 text-sm mt-1">Distribusi menu dan laporan harian</p>
                            </div>
                        </div>
                        <span class="text-xs font-bold bg-white/20 rounded-full px-3 py-1 whitespace-nowrap">{{ $laporanProgram['tanggal_hari_ini'] ?? now()->translatedFormat('l, d F Y') }}</span>
                    </div>

                    <div class="grid grid-cols-2 gap-3 mt-6">
                        <a href="{{ url('/laporan-harian?jenis=sppg&status=sudah') }}" class="rounded-2xl bg-white/20 px-4 py-3 hover:bg-white/30 transition-colors">
                            <p class="text-xs font-bold uppercase tracking-widest text-white/70">Sudah Lapor</p>
                            <p class="text-2xl font-extrabold mt-1">{{ number_format($laporanProgram['laporan_sppg_hari_ini'] ?? 0, 0, ',', '.') }}</p>
                        </a>
                        <a href="{{ url('/laporan-harian?jenis=sppg&status=belum') }}" class="rounded-2xl bg-white/20 px-4 py-3 hover:bg-white/30 transition-colors">
                            <p class="text-xs font-bold uppercase tracking-widest text-white/70">Belum Lapor</p>
                            <p class="text-2xl font-extrabold mt-1">{{ number_format($laporanProgram['sppg_belum_lapor'] ?? 0, 0, ',', '.') }}</p>
                        </a>
                    </div>

                    <div class="mt-8">
                        <a href="{{ url('/laporan-harian?jenis=sppg&status=sudah') }}" class="flex items-end gap-3 group">
                            <p class="text-6xl md:text-7xl font-black leading-none">{{ number_format($laporanProgram['laporan_sppg_hari_ini'] ?? 0, 0, ',', '.') }}</p>
                            <p class="text-white/80 font-bold mb-2 group-hover:text-white">dari {{ number_format($laporanProgram['total_sppg_aktif'] ?? 0, 0, ',', '.') }} SPPG</p>
                        </a>
                    </div>

                    <div class="mt-auto pt-8">
                        <div class="flex items-center justify-between text-sm font-bold mb-3">
                            <span>Capaian laporan</span>
                            <span>{{ $formatPercent($laporanProgram['persen_laporan_sppg'] ?? 0) }}%</span>
                        </div>
                        <div class="report-progress h-3 rounded-full bg-white/25 overflow-hidden">
                            <div class="h-full rounded-full bg-white" style="width: {{ $sppgProgress }}%"></div>
                        </div>
                    </div>
                </div>
            </article>

            <article class="highlight-card min-h-[300px] rounded-[1.75rem] p-7 md:p-8 text-white shadow-xl bg-gradient-to-br from-[#243b8f] via-[#4c5ec7] to-[#7e57c2] overflow-hidden relative">
                <div class="absolute -right-12 top-10 w-48 h-48 rounded-full bg-white/10"></div>
                <div class="absolute left-10 -bottom-12 w-32 h-32 rounded-full bg-white/10"></div>
                <div class="relative h-full flex flex-col">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined bg-white/20 rounded-2xl p-3 text-3xl">assignment_turned_in</span>
                            <div>
                                <h3 class="text-2xl font-extrabold font-headline">Laporan Sekolah Hari Ini</h3>
                                <p class="text-white/80 text-sm mt-1">Upload bukti menu dan siswa makan</p>
                            </div>
                        </div>
                        <span class="text-xs font-bold bg-white/20 rounded-full px-3 py-1 whitespace-nowrap">{{ $laporanProgram['tanggal_hari_ini'] ?? now()->translatedFormat('l, d F Y') }}</span>
                    </div>

                    <div class="grid grid-cols-2 gap-3 mt-6">
                        <a href="{{ url('/laporan-harian?jenis=sekolah&status=sudah') }}" class="rounded-2xl bg-white/20 px-4 py-3 hover:bg-white/30 transition-colors">
                            <p class="text-xs font-bold uppercase tracking-widest text-white/70">Sudah Lapor</p>
                            <p class="text-2xl font-extrabold mt-1">{{ number_format($laporanProgram['laporan_sekolah_hari_ini'] ?? 0, 0, ',', '.') }}</p>
                        </a>
                        <a href="{{ url('/laporan-harian?jenis=sekolah&status=belum') }}" class="rounded-2xl bg-white/20 px-4 py-3 hover:bg-white/30 transition-colors">
                            <p class="text-xs font-bold uppercase tracking-widest text-white/70">Belum Lapor</p>
                            <p class="text-2xl font-extrabold mt-1">{{ number_format($laporanProgram['sekolah_belum_lapor'] ?? 0, 0, ',', '.') }}</p>
                        </a>
                    </div>

                    <div class="mt-8">
                        <a href="{{ url('/laporan-harian?jenis=sekolah&status=sudah') }}" class="flex items-end gap-3 group">
                            <p class="text-6xl md:text-7xl font-black leading-none">{{ number_format($laporanProgram['laporan_sekolah_hari_ini'] ?? 0, 0, ',', '.') }}</p>
                            <p class="text-white/80 font-bold mb-2 group-hover:text-white">dari {{ number_format($laporanProgram['total_target_sekolah_hari_ini'] ?? 0, 0, ',', '.') }} sekolah</p>
                        </a>
                    </div>

                    <div class="mt-auto pt-8">
                        <div class="flex items-center justify-between text-sm font-bold mb-3">
                            <span>Capaian laporan</span>
                            <span>{{ $formatPercent($laporanProgram['persen_laporan_sekolah'] ?? 0) }}%</span>
                        </div>
                        <div class="report-progress h-3 rounded-full bg-white/25 overflow-hidden">
                            <div class="h-full rounded-full bg-white" style="width: {{ $sekolahProgress }}%"></div>
                        </div>
                    </div>
                </div>
            </article>
        </div>

        <div class="grid md:grid-cols-3 gap-5 mt-6">
            <a href="{{ url('/laporan-harian?jenis=sppg&status=sudah') }}" class="mini-stat-card bg-white rounded-2xl p-6 shadow-sm border border-surface-container-low hover:shadow-lg transition-all">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm text-on-surface-variant">Total SPPG Aktif</p>
                        <p class="text-4xl font-extrabold text-primary mt-2">{{ number_format($laporanProgram['total_sppg_aktif'] ?? 0, 0, ',', '.') }}</p>
                    </div>
                    <span class="material-symbols-outlined text-primary bg-primary-fixed rounded-2xl p-3">verified</span>
                </div>
            </a>
            <a href="{{ url('/laporan-harian?jenis=sppg&status=belum') }}" class="mini-stat-card bg-white rounded-2xl p-6 shadow-sm border border-surface-container-low hover:shadow-lg transition-all">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm text-on-surface-variant">SPPG Belum Lapor</p>
                        <p class="text-4xl font-extrabold text-tertiary mt-2">{{ number_format($laporanProgram['sppg_belum_lapor'] ?? 0, 0, ',', '.') }}</p>
                    </div>
                    <span class="material-symbols-outlined text-tertiary bg-tertiary-fixed rounded-2xl p-3">pending_actions</span>
                </div>
            </a>
            <div class="mini-stat-card bg-white rounded-2xl p-6 shadow-sm border border-surface-container-low">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm text-on-surface-variant">Total Porsi Didistribusi</p>
                        <p class="text-4xl font-extrabold text-secondary mt-2">{{ number_format($laporanProgram['total_porsi_hari_ini'] ?? 0, 0, ',', '.') }}</p>
                    </div>
                    <span class="material-symbols-outlined text-secondary bg-secondary-container rounded-2xl p-3">set_meal</span>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-20 px-6 bg-surface-container-low" id="laporan-harian-home">
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-5 mb-8">
            <div>
                <p class="text-xs uppercase tracking-widest text-outline font-bold mb-3">DETAIL LAPORAN HARIAN</p>
                <h2 class="text-4xl font-bold font-headline text-primary">Monitoring Sudah dan Belum Lapor</h2>
                <p class="text-on-surface-variant mt-3">Cari SPPG atau sekolah yang sudah/belum membuat laporan harian tanpa meninggalkan halaman Home.</p>
            </div>
            <a href="{{ url('/laporan-harian') }}" class="inline-flex items-center gap-2 text-primary font-bold">
                Buka halaman penuh
                <span class="material-symbols-outlined text-base">open_in_new</span>
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-surface-container-low p-4 md:p-5 mb-6">
            <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-5">
                <div class="flex flex-wrap gap-2" data-laporan-status-tabs>
                    <button type="button" data-status="sudah" class="laporan-status-tab px-4 py-2 rounded-full text-sm font-bold bg-primary text-white shadow-sm">Sudah Lapor</button>
                    <button type="button" data-status="belum" class="laporan-status-tab px-4 py-2 rounded-full text-sm font-bold bg-surface-container-low text-on-surface-variant hover:bg-primary-fixed hover:text-primary">Belum Lapor</button>
                    <button type="button" data-status="rekap" class="laporan-status-tab px-4 py-2 rounded-full text-sm font-bold bg-surface-container-low text-on-surface-variant hover:bg-primary-fixed hover:text-primary">Rekap Update</button>
                </div>

                <div class="flex flex-wrap gap-2" data-laporan-jenis-tabs>
                    <button type="button" data-jenis="sekolah" class="laporan-jenis-tab px-4 py-2 rounded-xl text-sm font-bold border bg-secondary text-white border-secondary">Sekolah</button>
                    <button type="button" data-jenis="sppg" class="laporan-jenis-tab px-4 py-2 rounded-xl text-sm font-bold border bg-white text-primary border-outline-variant hover:bg-secondary-container">SPPG</button>
                </div>
            </div>

            <form class="grid md:grid-cols-12 gap-3 mt-5" data-laporan-filter>
                <div class="md:col-span-3">
                    <label class="text-xs font-bold uppercase tracking-widest text-outline">Tanggal</label>
                    <input type="date" name="tanggal" value="{{ now()->toDateString() }}" class="mt-2 w-full rounded-xl border-outline-variant bg-surface-container-lowest focus:ring-primary focus:border-primary">
                </div>
                <div class="md:col-span-6">
                    <label class="text-xs font-bold uppercase tracking-widest text-outline">Cari nama atau lokasi</label>
                    <input type="text" name="search" value="" placeholder="Nama sekolah, SPPG, wilayah" class="mt-2 w-full rounded-xl border-outline-variant bg-surface-container-lowest focus:ring-primary focus:border-primary">
                </div>
                <div class="md:col-span-3 flex items-end gap-2">
                    <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 rounded-xl bg-primary text-white font-bold px-4 py-3 hover:bg-primary-container transition-colors">
                        <span class="material-symbols-outlined text-base">search</span>
                        Cari
                    </button>
                    <button type="button" data-laporan-reset class="inline-flex items-center justify-center rounded-xl border border-outline-variant text-primary font-bold px-4 py-3 hover:bg-primary-fixed transition-colors">
                        Reset
                    </button>
                </div>
            </form>
        </div>

        <div class="grid lg:grid-cols-3 gap-5 mb-6">
            <div class="lg:col-span-2 bg-primary text-white rounded-2xl p-6 shadow-sm">
                <p class="text-sm text-primary-fixed" data-laporan-total-label>Total Data Sekolah</p>
                <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mt-2">
                    <p class="text-5xl font-black" data-laporan-total-target>0</p>
                    <div class="grid grid-cols-2 gap-3 min-w-[260px]">
                        <div class="bg-white/20 rounded-xl p-3">
                            <p class="text-xs text-white/70">Sudah Lapor</p>
                            <p class="text-2xl font-extrabold" data-laporan-total-sudah>0</p>
                        </div>
                        <div class="bg-white/20 rounded-xl p-3">
                            <p class="text-xs text-white/70">Belum Lapor</p>
                            <p class="text-2xl font-extrabold" data-laporan-total-belum>0</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-surface-container-low">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm text-on-surface-variant">Data Ditampilkan</p>
                        <p class="text-4xl font-extrabold text-secondary mt-2" data-laporan-rows-total>0</p>
                    </div>
                    <span class="material-symbols-outlined text-secondary bg-secondary-container rounded-2xl p-3">fact_check</span>
                </div>
                <p class="text-xs text-on-surface-variant mt-4" data-laporan-date-label>{{ now()->translatedFormat('l, d F Y') }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-surface-container-low overflow-hidden relative" data-laporan-table-wrap>
            <div class="absolute inset-0 bg-white/70 backdrop-blur-sm hidden items-center justify-center z-10" data-laporan-loading>
                <div class="inline-flex items-center gap-2 rounded-full bg-primary text-white px-4 py-2 font-bold text-sm">
                    <span class="material-symbols-outlined text-base animate-pulse">sync</span>
                    Memuat data...
                </div>
            </div>
            <div data-laporan-table>
                <div class="px-5 py-12 text-center text-on-surface-variant">Memuat data laporan harian...</div>
            </div>
        </div>
    </div>
</section>

<section class="py-20 px-6">
    <div class="max-w-7xl mx-auto">
        <div class="mb-8">
            <p class="text-xs uppercase tracking-widest text-outline font-bold mb-3">Akses Cepat</p>
            <h2 class="text-4xl font-bold font-headline text-primary">Menu utama layanan publik</h2>
        </div>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">
            <a href="{{ url('/sekolah') }}" class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-lg transition-all"><span class="material-symbols-outlined text-primary mb-4">school</span><h3 class="font-bold text-lg text-primary">Daftar Sekolah</h3><p class="text-sm text-on-surface-variant mt-2">Lihat sekolah penerima manfaat MBG.</p></a>
            <a href="{{ url('/sppg') }}" class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-lg transition-all"><span class="material-symbols-outlined text-secondary mb-4">soup_kitchen</span><h3 class="font-bold text-lg text-primary">Daftar SPPG</h3><p class="text-sm text-on-surface-variant mt-2">Lihat dapur dan jejaring sekolah.</p></a>
            <a href="{{ url('/menu') }}" class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-lg transition-all"><span class="material-symbols-outlined text-tertiary mb-4">restaurant_menu</span><h3 class="font-bold text-lg text-primary">Menu & Gizi</h3><p class="text-sm text-on-surface-variant mt-2">Pantau menu harian dan ringkasan gizi.</p></a>
            <a href="{{ url('/category/all') }}" class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-lg transition-all"><span class="material-symbols-outlined text-primary mb-4">article</span><h3 class="font-bold text-lg text-primary">Berita & Edukasi</h3><p class="text-sm text-on-surface-variant mt-2">Baca informasi dan edukasi program.</p></a>
            <a href="{{ url('/contact') }}" class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-lg transition-all"><span class="material-symbols-outlined text-secondary mb-4">support_agent</span><h3 class="font-bold text-lg text-primary">Kontak & Pengaduan</h3><p class="text-sm text-on-surface-variant mt-2">Kirim masukan atau laporan masyarakat.</p></a>
            <a href="{{ url('/tracking') }}" class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-lg transition-all"><span class="material-symbols-outlined text-tertiary mb-4">manage_search</span><h3 class="font-bold text-lg text-primary">Lacak Pengaduan</h3><p class="text-sm text-on-surface-variant mt-2">Cek perkembangan tiket pengaduan.</p></a>
        </div>
    </div>
</section>

<section class="py-20 px-6 bg-surface-container-low">
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-10">
            <div>
                <p class="text-xs uppercase tracking-widest text-outline font-bold mb-3">Edukasi MBG</p>
                <h2 class="text-4xl font-bold font-headline text-primary">Informasi untuk masyarakat</h2>
            </div>
            <a href="{{ url('/category/all') }}" class="inline-flex items-center gap-2 text-primary font-bold">Berita & Edukasi <span class="material-symbols-outlined text-base">arrow_forward</span></a>
        </div>
        <div class="grid md:grid-cols-3 gap-6">
            @if($beritas->isNotEmpty())
                @foreach($beritas as $berita)
                    @php $beritaFoto = $imageUrl($berita->picture, $berita->updated_by); @endphp
                    <article class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-all flex flex-col">
                        <a href="{{ prettyUrl($berita) }}" class="aspect-video bg-surface-container-low overflow-hidden block">
                            @if($beritaFoto)
                                <img src="{{ $beritaFoto }}" alt="{{ $berita->title }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="h-full flex items-center justify-center text-primary">
                                    <span class="material-symbols-outlined text-6xl">article</span>
                                </div>
                            @endif
                        </a>
                        <div class="p-6 flex flex-col flex-1">
                            <div class="flex items-center gap-2 text-xs font-bold text-secondary mb-3">
                                <span class="material-symbols-outlined text-sm">calendar_today</span>
                                {{ $berita->tanggal ? date('d F Y', strtotime($berita->tanggal)) : '-' }}
                            </div>
                            <a href="{{ prettyUrl($berita) }}" class="text-xl font-bold text-primary leading-snug hover:underline">{{ $berita->title }}</a>
                            <p class="text-sm text-on-surface-variant mt-3 leading-relaxed flex-1">{!! \Illuminate\Support\Str::words(strip_tags(htmlspecialchars_decode($berita->content)), 22, '...') !!}</p>
                            <a href="{{ prettyUrl($berita) }}" class="mt-5 inline-flex items-center gap-2 text-primary font-bold text-sm">
                                Baca Selengkapnya
                                <span class="material-symbols-outlined text-base">arrow_forward</span>
                            </a>
                        </div>
                    </article>
                @endforeach
            @else
                @foreach($educationCards as $card)
                    <article class="bg-white rounded-2xl p-6 shadow-sm">
                        <span class="material-symbols-outlined text-primary mb-5">{{ $card['icon'] }}</span>
                        <h3 class="text-xl font-bold text-primary">{{ $card['title'] }}</h3>
                        <p class="text-sm text-on-surface-variant mt-3 leading-relaxed">{{ $card['body'] }}</p>
                    </article>
                @endforeach
            @endif
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const section = document.getElementById('laporan-harian-home');
    if (!section) {
        return;
    }

    const endpoint = '{{ url('/laporan-harian/data') }}';
    const state = {
        jenis: 'sekolah',
        status: 'sudah',
        tanggal: section.querySelector('[name="tanggal"]').value,
        search: '',
        page: 1
    };

    const table = section.querySelector('[data-laporan-table]');
    const loading = section.querySelector('[data-laporan-loading]');
    const filterForm = section.querySelector('[data-laporan-filter]');
    const searchInput = filterForm.querySelector('[name="search"]');
    const tanggalInput = filterForm.querySelector('[name="tanggal"]');
    const totalLabel = section.querySelector('[data-laporan-total-label]');
    const totalTarget = section.querySelector('[data-laporan-total-target]');
    const totalSudah = section.querySelector('[data-laporan-total-sudah]');
    const totalBelum = section.querySelector('[data-laporan-total-belum]');
    const rowsTotal = section.querySelector('[data-laporan-rows-total]');
    const dateLabel = section.querySelector('[data-laporan-date-label]');
    const formatter = new Intl.NumberFormat('id-ID');
    let searchTimer = null;

    function setLoading(active) {
        loading.classList.toggle('hidden', !active);
        loading.classList.toggle('flex', active);
    }

    function queryString(extra = {}) {
        const params = new URLSearchParams(Object.assign({}, state, extra));
        return params.toString();
    }

    function setActiveButtons() {
        section.querySelectorAll('.laporan-status-tab').forEach(function (button) {
            const active = button.dataset.status === state.status;
            button.className = active
                ? 'laporan-status-tab px-4 py-2 rounded-full text-sm font-bold bg-primary text-white shadow-sm'
                : 'laporan-status-tab px-4 py-2 rounded-full text-sm font-bold bg-surface-container-low text-on-surface-variant hover:bg-primary-fixed hover:text-primary';
        });

        section.querySelectorAll('.laporan-jenis-tab').forEach(function (button) {
            const active = button.dataset.jenis === state.jenis;
            button.className = active
                ? 'laporan-jenis-tab px-4 py-2 rounded-xl text-sm font-bold border bg-secondary text-white border-secondary'
                : 'laporan-jenis-tab px-4 py-2 rounded-xl text-sm font-bold border bg-white text-primary border-outline-variant hover:bg-secondary-container';
        });

        searchInput.placeholder = state.jenis === 'sekolah'
            ? 'Nama sekolah, SPPG, wilayah'
            : 'Nama SPPG, wilayah, alamat';
        totalLabel.textContent = state.jenis === 'sekolah' ? 'Total Data Sekolah' : 'Total Data SPPG';
    }

    function updateSummary(data) {
        totalTarget.textContent = formatter.format(data.totalTarget || 0);
        totalSudah.textContent = formatter.format(data.totalSudahLapor || 0);
        totalBelum.textContent = formatter.format(data.totalBelumLapor || 0);
        rowsTotal.textContent = formatter.format(data.rowsTotal || 0);
        dateLabel.textContent = data.tanggalLabel || '';
    }

    function loadData(extra = {}) {
        Object.assign(state, extra);
        setActiveButtons();
        setLoading(true);

        fetch(endpoint + '?' + queryString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Gagal memuat data laporan');
                }
                return response.json();
            })
            .then(function (data) {
                table.innerHTML = data.html;
                updateSummary(data);
            })
            .catch(function () {
                table.innerHTML = '<div class="px-5 py-12 text-center text-error">Data laporan harian gagal dimuat.</div>';
            })
            .finally(function () {
                setLoading(false);
            });
    }

    section.querySelectorAll('.laporan-status-tab').forEach(function (button) {
        button.addEventListener('click', function () {
            loadData({ status: button.dataset.status, page: 1 });
        });
    });

    section.querySelectorAll('.laporan-jenis-tab').forEach(function (button) {
        button.addEventListener('click', function () {
            loadData({ jenis: button.dataset.jenis, page: 1 });
        });
    });

    filterForm.addEventListener('submit', function (event) {
        event.preventDefault();
        loadData({
            tanggal: tanggalInput.value,
            search: searchInput.value.trim(),
            page: 1
        });
    });

    tanggalInput.addEventListener('change', function () {
        loadData({ tanggal: tanggalInput.value, page: 1 });
    });

    searchInput.addEventListener('input', function () {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function () {
            loadData({ search: searchInput.value.trim(), page: 1 });
        }, 450);
    });

    section.querySelector('[data-laporan-reset]').addEventListener('click', function () {
        searchInput.value = '';
        tanggalInput.value = '{{ now()->toDateString() }}';
        loadData({
            jenis: 'sekolah',
            status: 'sudah',
            tanggal: tanggalInput.value,
            search: '',
            page: 1
        });
    });

    table.addEventListener('click', function (event) {
        const link = event.target.closest('.laporan-pagination a');
        if (!link) {
            return;
        }

        event.preventDefault();
        const url = new URL(link.href);
        loadData({
            page: url.searchParams.get('page') || 1
        });
    });

    loadData();
});

document.addEventListener('click', function (event) {
    const link = event.target.closest('[data-photo-popup]');
    if (!link) {
        return;
    }

    event.preventDefault();
    openLaporanPhotoPopup(link.href, link.dataset.photoTitle || 'Foto laporan');
});

function openLaporanPhotoPopup(src, title) {
    let modal = document.getElementById('laporan-photo-popup');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'laporan-photo-popup';
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
@endpush
