@extends(getTheme('layouts.app'))

@section('content')
@php
    $menuImage = $todayMenu && $todayMenu->foto ? getPicture($todayMenu->foto, '', $todayMenu->updated_by) : null;
    $menuDate = $todayMenu && $todayMenu->tanggal ? $todayMenu->tanggal->translatedFormat('l, d F Y') : now()->translatedFormat('l, d F Y');
    $avgEnergi = $todayMenu ? (($todayMenu->kecil_energi + $todayMenu->besar_energi) / 2) : 0;
    $heroImage = 'https://images.unsplash.com/photo-1542810634-71277d95dcbb?auto=format&fit=crop&w=1400&q=80';
@endphp

<section class="pt-36 pb-20 px-6">
    <div class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-12 items-center">
        <div>
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-tertiary-fixed text-on-tertiary-fixed font-bold text-sm mb-6">
                <span class="w-2 h-2 rounded-full bg-tertiary"></span>
                Monitoring MBG Kota Serang
            </div>
            <h1 class="text-5xl md:text-6xl font-bold font-headline leading-tight text-primary mb-6">
                Makan Bergizi Gratis untuk generasi sehat dan siap belajar.
            </h1>
            <p class="text-lg text-on-surface-variant max-w-xl mb-8 leading-relaxed">
                Pantau dapur SPPG, sekolah penerima manfaat, menu harian, distribusi, dan laporan sekolah dalam satu halaman informasi publik.
            </p>
            <div class="flex flex-wrap gap-3">
                <a href="{{ url('/menu') }}" class="px-6 py-3 bg-primary text-white rounded-xl font-bold shadow-lg hover:bg-primary-container transition-colors">Lihat Menu Hari Ini</a>
                <a href="{{ url('/sekolah') }}" class="px-6 py-3 bg-surface-container-low text-primary rounded-xl font-bold hover:bg-primary-fixed transition-colors">Daftar Sekolah</a>
            </div>
        </div>
        <div class="relative rounded-[2rem] overflow-hidden shadow-2xl min-h-[420px]">
            <img src="{{ $heroImage }}" alt="Siswa menikmati makanan bergizi" class="absolute inset-0 w-full h-full object-cover">
            <div class="absolute inset-0 bg-primary/25"></div>
            <div class="absolute left-6 bottom-6 right-6 bg-white/90 backdrop-blur-md rounded-2xl p-5 shadow-lg">
                <p class="text-xs uppercase tracking-widest text-outline font-bold">Distribusi Hari Ini</p>
                <div class="mt-2 flex flex-wrap gap-6">
                    <div>
                        <p class="text-2xl font-extrabold text-primary">{{ number_format($ringkasanHariIni['total_porsi'], 0, ',', '.') }}</p>
                        <p class="text-xs text-on-surface-variant">porsi tercatat</p>
                    </div>
                    <div>
                        <p class="text-2xl font-extrabold text-secondary">{{ number_format($ringkasanHariIni['sudah_lapor'], 0, ',', '.') }}</p>
                        <p class="text-xs text-on-surface-variant">sudah lapor sekolah</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="bg-surface-container-low py-14 px-6">
    <div class="max-w-7xl mx-auto grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-surface-container-lowest p-6 rounded-2xl shadow-sm">
            <span class="material-symbols-outlined text-primary mb-4">school</span>
            <p class="text-3xl font-extrabold text-primary">{{ number_format($stats['total_sekolah'], 0, ',', '.') }}</p>
            <p class="text-sm text-on-surface-variant mt-1">Sekolah Terdata</p>
        </div>
        <div class="bg-surface-container-lowest p-6 rounded-2xl shadow-sm">
            <span class="material-symbols-outlined text-secondary mb-4">soup_kitchen</span>
            <p class="text-3xl font-extrabold text-secondary">{{ number_format($stats['total_sppg'], 0, ',', '.') }}</p>
            <p class="text-sm text-on-surface-variant mt-1">Unit SPPG</p>
        </div>
        <div class="bg-surface-container-lowest p-6 rounded-2xl shadow-sm">
            <span class="material-symbols-outlined text-primary-container mb-4">groups</span>
            <p class="text-3xl font-extrabold text-primary">{{ number_format($stats['total_penerima'], 0, ',', '.') }}</p>
            <p class="text-sm text-on-surface-variant mt-1">Penerima Manfaat</p>
        </div>
        <div class="bg-surface-container-lowest p-6 rounded-2xl shadow-sm">
            <span class="material-symbols-outlined text-tertiary mb-4">restaurant_menu</span>
            <p class="text-3xl font-extrabold text-tertiary">{{ number_format($stats['total_menu'], 0, ',', '.') }}</p>
            <p class="text-sm text-on-surface-variant mt-1">Menu Harian</p>
        </div>
    </div>
</section>

<section class="py-20 px-6">
    <div class="max-w-7xl mx-auto grid lg:grid-cols-12 gap-10 items-start">
        <div class="lg:col-span-5">
            <p class="text-xs uppercase tracking-widest text-outline font-bold mb-3">Tentang MBG</p>
            <h2 class="text-4xl font-bold font-headline text-primary leading-tight">Program pemenuhan gizi yang dipantau dari dapur sampai sekolah.</h2>
        </div>
        <div class="lg:col-span-7 bg-surface-container-lowest rounded-2xl p-8 shadow-sm">
            <p class="text-on-surface-variant leading-relaxed">
                Makan Bergizi Gratis mendukung pemenuhan kebutuhan gizi peserta didik melalui penyediaan makanan yang disiapkan oleh SPPG, didistribusikan ke sekolah, lalu dipantau melalui laporan harian dari SPPG dan sekolah.
            </p>
            <div class="grid sm:grid-cols-3 gap-4 mt-8">
                <div class="bg-primary-fixed rounded-xl p-4">
                    <p class="font-bold text-primary">Higienis</p>
                    <p class="text-xs text-on-surface-variant mt-1">Dapur dan proses produksi terpantau.</p>
                </div>
                <div class="bg-secondary-container/30 rounded-xl p-4">
                    <p class="font-bold text-secondary">Bergizi</p>
                    <p class="text-xs text-on-surface-variant mt-1">Menu dicatat dengan komponen nutrisi.</p>
                </div>
                <div class="bg-tertiary-fixed/50 rounded-xl p-4">
                    <p class="font-bold text-tertiary">Terukur</p>
                    <p class="text-xs text-on-surface-variant mt-1">Distribusi dan laporan sekolah dibandingkan.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="pb-20 px-6">
    <div class="max-w-7xl mx-auto grid md:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-surface-container-lowest rounded-2xl p-6 shadow-sm border-t-4 border-primary">
            <span class="material-symbols-outlined text-primary mb-4">flag</span>
            <h3 class="text-xl font-bold text-primary mb-2">Misi</h3>
            <p class="text-sm text-on-surface-variant leading-relaxed">Menyajikan makanan bergizi dan aman bagi peserta didik secara rutin.</p>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl p-6 shadow-sm border-t-4 border-secondary">
            <span class="material-symbols-outlined text-secondary mb-4">target</span>
            <h3 class="text-xl font-bold text-primary mb-2">Tujuan</h3>
            <p class="text-sm text-on-surface-variant leading-relaxed">Meningkatkan konsentrasi belajar, kesehatan, dan pemerataan akses gizi.</p>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl p-6 shadow-sm border-t-4 border-tertiary">
            <span class="material-symbols-outlined text-tertiary mb-4">diversity_3</span>
            <h3 class="text-xl font-bold text-primary mb-2">Sasaran</h3>
            <p class="text-sm text-on-surface-variant leading-relaxed">Sekolah dan peserta didik penerima manfaat yang terhubung dengan SPPG.</p>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl p-6 shadow-sm border-t-4 border-primary-container">
            <span class="material-symbols-outlined text-primary-container mb-4">health_and_safety</span>
            <h3 class="text-xl font-bold text-primary mb-2">Manfaat</h3>
            <p class="text-sm text-on-surface-variant leading-relaxed">Data program lebih transparan, mudah dipantau, dan cepat ditindaklanjuti.</p>
        </div>
    </div>
</section>

<section class="bg-surface-container-low py-20 px-6">
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-10">
            <div>
                <p class="text-xs uppercase tracking-widest text-outline font-bold mb-3">Menu Harian Terbaru</p>
                <h2 class="text-4xl font-bold font-headline text-primary">{{ $todayMenu->nama ?? 'Menu belum tersedia' }}</h2>
                <p class="text-on-surface-variant mt-2">{{ $menuDate }}</p>
            </div>
            <a href="{{ url('/menu') }}" class="inline-flex items-center gap-2 text-primary font-bold">
                Lihat Semua Menu
                <span class="material-symbols-outlined text-base">arrow_forward</span>
            </a>
        </div>
        <div class="grid lg:grid-cols-12 gap-8 bg-surface-container-lowest rounded-[2rem] overflow-hidden shadow-sm">
            <div class="lg:col-span-5 min-h-[320px] bg-surface-container-low">
                @if($menuImage)
                    <img src="{{ $menuImage }}" alt="{{ $todayMenu->nama }}" class="w-full h-full object-cover">
                @else
                    <div class="h-full min-h-[320px] flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined text-7xl">restaurant_menu</span>
                    </div>
                @endif
            </div>
            <div class="lg:col-span-7 p-8 lg:p-10">
                <div class="flex flex-wrap gap-2 mb-5">
                    <span class="px-3 py-1 rounded-full bg-secondary-container text-on-secondary-container text-xs font-bold">MENU SPPG</span>
                    <span class="px-3 py-1 rounded-full bg-primary-fixed text-on-primary-fixed text-xs font-bold">{{ optional(optional($todayMenu)->sppg)->nama ?? 'SPPG belum tersedia' }}</span>
                </div>
                <p class="text-on-surface-variant leading-relaxed mb-8">{{ $todayMenu->deskripsi ?? 'Data menu harian terbaru belum tersedia.' }}</p>
                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-surface-container-low rounded-xl p-4">
                        <p class="text-[10px] uppercase tracking-widest text-outline font-bold">Energi</p>
                        <p class="text-xl font-extrabold text-primary mt-1">{{ number_format($avgEnergi, 0, ',', '.') }} <span class="text-xs font-normal">kkal</span></p>
                    </div>
                    <div class="bg-surface-container-low rounded-xl p-4">
                        <p class="text-[10px] uppercase tracking-widest text-outline font-bold">Protein</p>
                        <p class="text-xl font-extrabold text-primary mt-1">{{ number_format($todayMenu->besar_protein ?? 0, 0, ',', '.') }} <span class="text-xs font-normal">g</span></p>
                    </div>
                    <div class="bg-surface-container-low rounded-xl p-4">
                        <p class="text-[10px] uppercase tracking-widest text-outline font-bold">Karbo</p>
                        <p class="text-xl font-extrabold text-primary mt-1">{{ number_format($todayMenu->besar_karbohidrat ?? 0, 0, ',', '.') }} <span class="text-xs font-normal">g</span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-20 px-6">
    <div class="max-w-7xl mx-auto">
        <div class="mb-8">
            <p class="text-xs uppercase tracking-widest text-outline font-bold mb-3">Ringkasan Hari Ini</p>
            <h2 class="text-4xl font-bold font-headline text-primary">Distribusi dan laporan sekolah</h2>
        </div>
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-5">
            <div class="bg-primary text-white rounded-2xl p-6 shadow-sm">
                <p class="text-sm opacity-80">Total Distribusi</p>
                <p class="text-4xl font-extrabold mt-2">{{ number_format($ringkasanHariIni['total_distribusi'], 0, ',', '.') }}</p>
            </div>
            <div class="bg-surface-container-lowest rounded-2xl p-6 shadow-sm">
                <p class="text-sm text-on-surface-variant">Sudah Lapor Sekolah</p>
                <p class="text-4xl font-extrabold text-secondary mt-2">{{ number_format($ringkasanHariIni['sudah_lapor'], 0, ',', '.') }}</p>
            </div>
            <div class="bg-surface-container-lowest rounded-2xl p-6 shadow-sm">
                <p class="text-sm text-on-surface-variant">Belum Lapor Sekolah</p>
                <p class="text-4xl font-extrabold text-tertiary mt-2">{{ number_format($ringkasanHariIni['belum_lapor'], 0, ',', '.') }}</p>
            </div>
            <div class="bg-surface-container-lowest rounded-2xl p-6 shadow-sm">
                <p class="text-sm text-on-surface-variant">Laporan Sekolah</p>
                <p class="text-4xl font-extrabold text-primary mt-2">{{ number_format($ringkasanHariIni['laporan_sekolah'], 0, ',', '.') }}</p>
            </div>
        </div>
    </div>
</section>

<section class="bg-surface-container-low py-20 px-6">
    <div class="max-w-7xl mx-auto">
        <div class="mb-8">
            <p class="text-xs uppercase tracking-widest text-outline font-bold mb-3">Akses Cepat</p>
            <h2 class="text-4xl font-bold font-headline text-primary">Jelajahi monitoring MBG</h2>
        </div>
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-5">
            <a href="{{ url('/sekolah') }}" class="bg-surface-container-lowest rounded-2xl p-6 shadow-sm hover:shadow-lg transition-all">
                <span class="material-symbols-outlined text-primary mb-5">school</span>
                <h3 class="font-bold text-lg text-primary">Daftar Sekolah</h3>
                <p class="text-sm text-on-surface-variant mt-2">Lihat sekolah penerima manfaat.</p>
            </a>
            <a href="{{ url('/sppg') }}" class="bg-surface-container-lowest rounded-2xl p-6 shadow-sm hover:shadow-lg transition-all">
                <span class="material-symbols-outlined text-secondary mb-5">soup_kitchen</span>
                <h3 class="font-bold text-lg text-primary">Daftar SPPG</h3>
                <p class="text-sm text-on-surface-variant mt-2">Lihat dapur dan jejaring layanan.</p>
            </a>
            <a href="{{ url('/menu') }}" class="bg-surface-container-lowest rounded-2xl p-6 shadow-sm hover:shadow-lg transition-all">
                <span class="material-symbols-outlined text-tertiary mb-5">restaurant_menu</span>
                <h3 class="font-bold text-lg text-primary">Menu Harian</h3>
                <p class="text-sm text-on-surface-variant mt-2">Cek menu dan informasi gizi.</p>
            </a>
            <a href="{{ url('/contact') }}" class="bg-surface-container-lowest rounded-2xl p-6 shadow-sm hover:shadow-lg transition-all">
                <span class="material-symbols-outlined text-primary-container mb-5">support_agent</span>
                <h3 class="font-bold text-lg text-primary">Pengaduan</h3>
                <p class="text-sm text-on-surface-variant mt-2">Sampaikan laporan atau keluhan.</p>
            </a>
        </div>
    </div>
</section>

<section class="py-20 px-6">
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-10">
            <div>
                <p class="text-xs uppercase tracking-widest text-outline font-bold mb-3">Berita/Edukasi</p>
                <h2 class="text-4xl font-bold font-headline text-primary">Informasi terbaru MBG</h2>
            </div>
            <a href="{{ url('/category/all') }}" class="inline-flex items-center gap-2 text-primary font-bold">
                Lihat Berita
                <span class="material-symbols-outlined text-base">arrow_forward</span>
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach(latestPost(3) as $latestpost)
                <article class="bg-surface-container-lowest rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-all">
                    <div class="aspect-video bg-surface-container-low overflow-hidden">
                        <img alt="{{ $latestpost->title }}" class="w-full h-full object-cover" src="{{ getPicture($latestpost->picture, '', $latestpost->updated_by) }}" />
                    </div>
                    <div class="p-6">
                        <div class="flex items-center gap-2 text-xs font-bold text-secondary mb-3">
                            <span class="material-symbols-outlined text-sm">calendar_today</span>
                            {{ date('d F Y', strtotime($latestpost->tanggal)) }}
                        </div>
                        <h3 class="text-xl font-bold font-headline leading-tight text-primary">{{ $latestpost->title }}</h3>
                        <p class="text-on-surface-variant text-sm mt-3 leading-relaxed">{!! \Illuminate\Support\Str::words(strip_tags(htmlspecialchars_decode($latestpost->content)), 18, '...') !!}</p>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endsection
