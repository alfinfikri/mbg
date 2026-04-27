@extends(getTheme('layouts.app'))

@section('content')
<section class="relative pt-32 pb-20 px-8 overflow-hidden">
    <div class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-12 items-center">
        <div class="z-10">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-tertiary-fixed text-on-tertiary-fixed font-semibold text-sm mb-6">
                <span class="w-2 h-2 rounded-full bg-tertiary animate-pulse"></span>
                Program Nasional Presiden RI
            </div>
            <h1 class="text-6xl md:text-7xl font-bold font-headline leading-[1.1] text-primary mb-8">
                Nutrisi <span class="text-secondary">Terbaik</span> Untuk Masa Depan Bangsa.
            </h1>
            <p class="text-lg text-on-surface-variant max-w-xl mb-10 leading-relaxed">
                Membangun generasi emas Indonesia melalui penyediaan makanan bergizi gratis yang higienis, lezat, dan seimbang bagi seluruh siswa sekolah.
            </p>
            <div class="flex flex-wrap gap-4">
                <a href="{{ url('/menu') }}">
                    <button class="px-8 py-4 bg-primary text-on-primary rounded-xl font-bold text-lg shadow-lg hover:shadow-primary/20 transition-all active:scale-95">
                        Lihat Menu Hari Ini
                    </button>
                </a>
            </div>
        </div>
        <div class="relative">
            <div class="absolute -top-12 -left-12 w-64 h-64 bg-secondary-fixed/30 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-12 -right-12 w-80 h-80 bg-primary-fixed/30 rounded-full blur-3xl"></div>
            <div class="relative rounded-[2.5rem] overflow-hidden shadow-2xl rotate-2">
                <img alt="Children enjoying healthy food" class="w-full h-[500px] object-cover" data-alt="Happy Indonesian school children in uniforms sitting together in a bright cafeteria, eating colorful balanced meals with smiles and laughter, natural morning sunlight" src="https://lh3.googleusercontent.com/aida-public/AB6AXuClUzcE4ss-yV-toP9lrPRDou0zs3VOnu0Kj6rWy3iWWOD_teEdxUkZ_MVzV59l_YEYIkAi0cA5g0KwGCGXlITI9zC1UU1fuxpZcAb2_D8HK5qKBycHffrzL9s8YMAb2sM9tJnSiG8BFTAfb6VRGNmMXpGSfL565k5DwFDDSuyYz_hvcHu2H3R4-7CKt-i0o3x5fPyRix-aCf-syGuKrrcaamlB8fe6a8ntOq_phze198vgObkVplIBydRVt80KGa-IUVSsiMeu1PVp" />
                <div class="absolute inset-0 bg-gradient-to-t from-primary/40 to-transparent"></div>
            </div>

            <!-- Stats Overlay Card -->
            <div class="absolute bottom-8 -left-8 bg-surface-container-lowest p-6 rounded-2xl shadow-[0px_12px_32px_rgba(24,28,30,0.1)] max-w-xs">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-secondary-fixed flex items-center justify-center text-on-secondary-fixed">
                        <span class="material-symbols-outlined" data-icon="restaurant" style="font-variation-settings: 'FILL' 1;">restaurant</span>
                    </div>
                    <div>
                        <p class="text-sm font-label text-on-surface-variant">Menu Sehat</p>
                        <p class="text-lg font-bold font-headline">100% Higienis</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- Stats Section -->
<section class="bg-surface-container-low py-16 px-8">
    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 md:gap-6">
            <!-- Stat 1: Sekolah -->
            <div class="bg-surface-container-lowest p-6 md:p-8 rounded-3xl text-center shadow-sm hover:shadow-md transition-shadow">
                <h3 class="text-3xl md:text-4xl font-bold font-headline text-primary mb-2">12.450</h3>
                <p class="text-slate-500 font-semibold text-sm md:text-base">Jumlah Sekolah</p>
            </div>
            <!-- Stat 2: Siswa -->
            <div class="bg-surface-container-lowest p-6 md:p-8 rounded-3xl text-center shadow-sm hover:shadow-md transition-shadow border-t-4 border-secondary">
                <h3 class="text-3xl md:text-4xl font-bold font-headline text-secondary mb-2">3.2M</h3>
                <p class="text-slate-500 font-semibold text-sm md:text-base">Siswa Penerima</p>
            </div>
            <!-- Stat 3: Menu Harian -->
            <div class="bg-surface-container-lowest p-6 md:p-8 rounded-3xl text-center shadow-sm hover:shadow-md transition-shadow">
                <h3 class="text-3xl md:text-4xl font-bold font-headline text-tertiary mb-2">85</h3>
                <p class="text-slate-500 font-semibold text-sm md:text-base">Varian Menu</p>
            </div>
            <!-- Stat 4: Posyandu -->
            <div class="bg-surface-container-lowest p-6 md:p-8 rounded-3xl text-center shadow-sm hover:shadow-md transition-shadow border-t-4 border-primary">
                <h3 class="text-3xl md:text-4xl font-bold font-headline text-primary mb-2">4.200</h3>
                <p class="text-slate-500 font-semibold text-sm md:text-base">Posyandu Terpadu</p>
            </div>
            <!-- Stat 5: Total Penerima -->
            <div class="col-span-2 md:col-span-1 bg-surface-container-lowest p-6 md:p-8 rounded-3xl text-center shadow-sm hover:shadow-md transition-shadow border-t-4 border-tertiary">
                <h3 class="text-3xl md:text-4xl font-bold font-headline text-tertiary mb-2">3.5M</h3>
                <p class="text-slate-500 font-semibold text-sm md:text-base">Total Penerima</p>
            </div>
        </div>
    </div>
</section>

<!-- Today's Menu Highlight (Bento Layout) -->
<section class="py-24 px-8">
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-12 gap-4">
            <div>
                <h2 class="text-4xl font-bold font-headline text-primary mb-4">Menu Unggulan Hari Ini</h2>
                <p class="text-on-surface-variant">Standar gizi tinggi yang disusun oleh ahli nutrisi bersertifikasi.</p>
            </div>
            <a class="text-primary font-bold flex items-center gap-2 group" href="#">
                Lihat Jadwal Mingguan
                <span class="material-symbols-outlined group-hover:translate-x-1 transition-transform" data-icon="arrow_forward">arrow_forward</span>
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <!-- Main Menu Card -->
            
            <div class="md:col-span-2 md:row-span-2 bg-surface-container-lowest rounded-3xl overflow-hidden shadow-sm group">

                {{-- Gambar --}}
                <div class="h-80 overflow-hidden">
                    <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                        src="{{ $todayMenu ? getPicture($todayMenu->foto, '', $todayMenu->updated_by) : asset('images/default.jpg') }}" />
                </div>

                <div class="p-8">

                    {{-- Label --}}
                    <span class="px-3 py-1 bg-secondary-container text-on-secondary-container text-xs font-bold rounded-full mb-4 inline-block">
                        MENU HARI INI
                    </span>

                    <span class="px-3 py-1 bg-secondary-container text-on-secondary-container text-xs font-bold rounded-full mb-4 inline-block">
                        {{ $todayMenu->sppg->nama }}
                    </span>

                    {{-- Nama Menu --}}
                    <h3 class="text-2xl font-bold font-headline mb-2">
                        {{ $todayMenu->nama ?? 'Menu belum tersedia' }}
                    </h3>

                    {{-- Deskripsi --}}
                    <p class="text-on-surface-variant mb-6">
                        {{ $todayMenu->deskripsi ?? 'Deskripsi belum tersedia' }}
                    </p>

                    {{-- Nutrisi --}}
                    <div class="flex gap-6 border-t border-surface-container pt-6">

                        {{-- Kalori --}}
                        <div>
                            <span class="text-xs text-slate-400 block mb-1 uppercase tracking-wider font-bold">Kalori</span>
                            <span class="font-bold">
                                {{ ($todayMenu->kecil_energi + $todayMenu->besar_energi) / 2 ?? 0 }} kkal
                            </span>
                        </div>

                        {{-- Protein --}}
                        <div>
                            <span class="text-xs text-slate-400 block mb-1 uppercase tracking-wider font-bold">Protein</span>
                            <span class="font-bold">
                                {{ $todayMenu->besar_protein ?? 0 }} g
                            </span>
                        </div>

                        {{-- Karbo --}}
                        <div>
                            <span class="text-xs text-slate-400 block mb-1 uppercase tracking-wider font-bold">Karbo</span>
                            <span class="font-bold">
                                {{ $todayMenu->besar_karbohidrat ?? 0 }} g
                            </span>
                        </div>

                    </div>
                </div>
            </div>
            <!-- Side Menu 1 -->
            <div class="bg-surface-container-lowest rounded-3xl p-6 shadow-sm border-l-4 border-tertiary">
                <div class="w-12 h-12 bg-tertiary-fixed rounded-2xl flex items-center justify-center text-on-tertiary-fixed mb-6">
                    <span class="material-symbols-outlined" data-icon="breakfast_dining">breakfast_dining</span>
                </div>
                <h4 class="text-xl font-bold font-headline mb-2">Nasi Merah Organik</h4>
                <p class="text-sm text-on-surface-variant">Karbohidrat kompleks untuk energi belajar yang stabil sepanjang hari.</p>
            </div>
            <!-- Side Menu 2 -->
            <div class="bg-surface-container-lowest rounded-3xl p-6 shadow-sm">
                <div class="w-12 h-12 bg-secondary-fixed rounded-2xl flex items-center justify-center text-on-secondary-fixed mb-6">
                    <span class="material-symbols-outlined" data-icon="eco">eco</span>
                </div>
                <h4 class="text-xl font-bold font-headline mb-2">Buah Naga Segar</h4>
                <p class="text-sm text-on-surface-variant">Kaya akan antioksidan dan vitamin C untuk meningkatkan imun tubuh.</p>
            </div>
            <!-- Side Menu 3 (Wide) -->
            <div class="md:col-span-2 bg-primary text-on-primary rounded-3xl p-8 relative overflow-hidden flex items-center">
                <div class="relative z-10">
                    <h4 class="text-2xl font-bold font-headline mb-2">Kualitas Terjamin</h4>
                    <p class="opacity-80 max-w-sm">Semua bahan baku berasal dari mitra lokal UMKM dengan pengawasan ketat Badan Gizi Nasional.</p>
                </div>
                <div class="absolute -right-8 -bottom-8 opacity-20">
                    <span class="material-symbols-outlined text-[160px]" data-icon="verified" style="font-variation-settings: 'FILL' 1;">verified</span>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Latest Announcements -->
<section class="bg-surface-container-low py-24 px-8">
    <div class="max-w-7xl mx-auto">
        <div class="mb-12">
            <h2 class="text-4xl font-bold font-headline text-primary mb-4">Berita &amp; Edukasi</h2>
            <p class="text-on-surface-variant">Update terbaru mengenai pelaksanaan program makanan bergizi gratis.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- News Item -->
             @foreach(latestPost(3) as $key => $latestpost)
                <article class="flex flex-col gap-4 group">
                    <div class="aspect-video rounded-2xl overflow-hidden mb-2">
                        <img alt="Logistics distribution" class="w-full h-full object-cover group-hover:scale-105 transition-transform" data-alt="{!! \Illuminate\Support\Str::words(strip_tags(htmlspecialchars_decode($latestpost->content)), 5, '...') !!}" src="{{ getPicture($latestpost->picture, '', $latestpost->updated_by) }}" />
                    </div>
                    <div class="flex items-center gap-3 text-sm font-semibold text-secondary">
                        <span class="material-symbols-outlined text-sm" data-icon="calendar_today">calendar_today</span>
                        {{ date('d F Y', strtotime($latestpost->tanggal)) }}
                    </div>
                    <h3 class="text-xl font-bold font-headline leading-tight group-hover:text-primary transition-colors">{{ $latestpost->title }}</h3>
                    <p class="text-on-surface-variant text-sm line-clamp-2 leading-relaxed">{!! \Illuminate\Support\Str::words(strip_tags(htmlspecialchars_decode($latestpost->content)), 20, '...') !!}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endsection