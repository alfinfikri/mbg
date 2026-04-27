@extends(getTheme('layouts.app'))

@section('content')
<header class="relative h-[500px] w-full overflow-hidden mb-12">
    <img alt="Healthy school meal spread with fresh vegetables and rice" class="absolute inset-0 w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAeuTglsWyBKvGUqfWDbYThHA2k49eljnCFVlUtz9R7RaO3044sMyHW24FArOb4F-MnDFivYifS-CqOcLQfpiu57gv0y8l3LKQ34dbgf4PbgxzwE1jqn_kws-Mbzqd62pqPK2DM9UrO0pISzWr74kj8pMtixNWlRtaIwb9pxVNtxfGiuwdZ41CGxACpj4PI4OWckp1V-_PWHskwzOMVO4EaFTRZDSwAMgDfoUJNqNXELpMPV2-tS_Zqvsbki-peCoDw6igsUK3sjCKW" />
    <div class="absolute inset-0 bg-black/40"></div>
    <div class="relative h-full max-w-7xl mx-auto px-6 flex flex-col justify-center items-start text-white">
        <div class="bg-primary/80 backdrop-blur-sm px-4 py-1.5 rounded-lg text-xs font-bold uppercase tracking-[0.2em] mb-4">Badan Gizi Nasional</div>
        <h1 class="text-5xl md:text-6xl font-extrabold max-w-2xl leading-tight mb-6">Menu Gizi Sehat untuk Generasi Emas</h1>
        <p class="text-lg text-white/90 max-w-xl leading-relaxed">Menjamin asupan nutrisi berkualitas bagi jutaan siswa Indonesia untuk masa depan yang lebih cerah dan mandiri.</p>
    </div>
</header>
<section class="py-12 max-w-7xl mx-auto px-6">
    
    <!-- Hero Section: Compact Today's Menu -->
    <section class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-8 -mt-20 relative z-10">
        <div
            class="lg:col-span-8 bg-surface-container-lowest rounded-2xl overflow-hidden editorial-shadow flex flex-col md:flex-row border border-white/20">
            <div class="md:w-1/2 relative h-64 md:h-auto">
                <img class="w-full h-full object-cover"
                    data-alt="Close-up of a golden brown Fuyunghai omelette topped with thick sweet and sour sauce and green peas, garnished with fresh herbs"
                    src="{{ getPicture($todayMenu->foto, '', $todayMenu->updated_by) }}" />
                <div class="absolute top-4 left-4 bg-tertiary-container text-on-tertiary-container px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">
                    {{ $todayMenu ? \Carbon\Carbon::parse($todayMenu->tanggal)->translatedFormat('l, d F Y') : '-' }}
                </div>
            </div>
            <div class="md:w-1/2 p-8 flex flex-col justify-center">
                <h2 class="text-3xl font-extrabold text-primary mb-3 leading-tight">{{ $todayMenu->nama ?? 'Menu belum tersedia' }}</h2>
                <p class="text-on-surface-variant text-sm mb-6 leading-relaxed">{{ $todayMenu ? $todayMenu->sppg->nama : '-' }}</p>
                @php
                    $items = $todayMenu ? explode(',', $todayMenu->deskripsi) : [];
                @endphp

                <div class="grid grid-cols-2 gap-4">
                    @forelse($items as $item)
                        <div class="flex items-center gap-2 text-xs font-medium text-on-surface">
                            <span class="material-symbols-outlined text-secondary text-sm">check_circle</span>
                            {{ trim($item) }}
                        </div>
                    @empty
                        <div class="col-span-2 text-sm text-gray-500">
                            Menu belum tersedia
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
        <!-- Quality & Standards Quick Grid -->
        <div class="lg:col-span-4 grid grid-cols-2 gap-4">
            <div
                class="bg-white p-4 rounded-2xl flex flex-col items-center text-center justify-center border-l-4 border-primary editorial-shadow">
                <span class="material-symbols-outlined text-primary mb-2">verified_user</span>
                <h3 class="text-sm font-bold">Izin BPOM</h3>
                <p class="text-[10px] text-on-surface-variant">Terverifikasi MD/ML</p>
            </div>
            <div
                class="bg-white p-4 rounded-2xl flex flex-col items-center text-center justify-center border-l-4 border-secondary editorial-shadow">
                <span class="material-symbols-outlined text-secondary mb-2">clean_hands</span>
                <h3 class="text-sm font-bold">Higiene 100%</h3>
                <p class="text-[10px] text-on-surface-variant">Standar Sanitasi</p>
            </div>
            <div
                class="bg-white p-4 rounded-2xl flex flex-col items-center text-center justify-center border-l-4 border-tertiary-container editorial-shadow">
                <span class="material-symbols-outlined text-tertiary-container mb-2">no_food</span>
                <h3 class="text-sm font-bold">Non-Pengawet</h3>
                <p class="text-[10px] text-on-surface-variant">Bahan Segar Alami</p>
            </div>
            <div
                class="bg-white p-4 rounded-2xl flex flex-col items-center text-center justify-center border-l-4 border-error editorial-shadow">
                <span class="material-symbols-outlined text-error mb-2">eco</span>
                <h3 class="text-sm font-bold">Ramah Lingkungan</h3>
                <p class="text-[10px] text-on-surface-variant">Kemasan Biodegradable</p>
            </div>
        </div>
    </section>
    <!-- Nutrition & Comparative Section -->
    <section class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-8">
        <div class="lg:col-span-8 bg-surface-container-lowest p-8 rounded-2xl editorial-shadow">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-xl font-bold flex items-center gap-3">
                    <span class="w-1.5 h-6 bg-primary rounded-full"></span>
                    Analisis Nutrisi Per Porsi
                </h2>
                <span
                    class="text-xs bg-surface-container-high px-4 py-1.5 rounded-full text-on-surface-variant font-semibold">Data
                    Real-time MBG</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-xs font-bold text-on-surface-variant border-b border-surface-variant">
                            <th class="pb-4">METRIK GIZI</th>
                            <th class="pb-4">PORSI KECIL (SD)</th>
                            <th class="pb-4">PORSI BESAR (SMP/SMA)</th>
                            <th class="pb-4 text-right">AKG (%)</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <tr class="border-b border-surface-variant/50">
                            <td class="py-4 font-semibold">Energi (kkal)</td>
                            <td class="py-4">{{ $todayMenu->kecil_energi ?? 0 }} kkal</td>
                            <td class="py-4">{{ $todayMenu->besar_energi ?? 0 }} kkal</td>
                            <td class="py-4">
                                <div class="w-24 bg-surface-container ml-auto rounded-full h-2 overflow-hidden">
                                    <div class="bg-primary h-full" style="width: 35%"></div>
                                </div>
                            </td>
                        </tr>
                        <tr class="border-b border-surface-variant/50">
                            <td class="py-4 font-semibold">Protein (g)</td>
                            <td class="py-4">{{ $todayMenu->kecil_protein ?? 0 }} g</td>
                            <td class="py-4">{{ $todayMenu->besar_protein ?? 0 }} g</td>
                            <td class="py-4">
                                <div class="w-24 bg-surface-container ml-auto rounded-full h-2 overflow-hidden">
                                    <div class="bg-secondary h-full" style="width: 45%"></div>
                                </div>
                            </td>
                        </tr>
                        <tr class="border-b border-surface-variant/50">
                            <td class="py-4 font-semibold">Lemak (g)</td>
                            <td class="py-4">{{ $todayMenu->kecil_lemak ?? 0 }} g</td>
                            <td class="py-4">{{ $todayMenu->besar_lemak ?? 0 }} g</td>
                            <td class="py-4">
                                <div class="w-24 bg-surface-container ml-auto rounded-full h-2 overflow-hidden">
                                    <div class="bg-tertiary-container h-full" style="width: 25%"></div>
                                </div>
                            </td>
                        </tr>
                        <tr class="border-b border-surface-variant/50">
                            <td class="py-4 font-semibold">Karbohidrat (g)</td>
                            <td class="py-4">{{ $todayMenu->kecil_karbohidrat ?? 0 }} g</td>
                            <td class="py-4">{{ $todayMenu->besar_karbohidrat ?? 0 }} g</td>
                            <td class="py-4">
                                <div class="w-24 bg-surface-container ml-auto rounded-full h-2 overflow-hidden">
                                    <div class="bg-primary-container h-full" style="width: 30%"></div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-4 font-semibold">Serat (g)</td>
                            <td class="py-4">{{ $todayMenu->kecil_serat ?? 0 }} g</td>
                            <td class="py-4">{{ $todayMenu->besar_serat ?? 0 }} g</td>
                            <td class="py-4">
                                <div class="w-24 bg-surface-container ml-auto rounded-full h-2 overflow-hidden">
                                    <div class="bg-secondary-container h-full" style="width: 20%"></div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Sidebar: Nutrition Tips -->
        <div class="lg:col-span-4 flex flex-col gap-6">
            <div class="bg-primary text-on-primary p-8 rounded-2xl shadow-lg">
                <h3 class="text-lg font-bold mb-5 flex items-center gap-2">
                    <span class="material-symbols-outlined">lightbulb</span> Tips Edukasi Gizi
                </h3>
                <ul class="space-y-4 text-sm opacity-90">
                    <li class="flex gap-3">
                        <span class="text-secondary-fixed">✅</span>
                        <span>Makan tepat waktu membantu meningkatkan konsentrasi belajar siswa hingga 25%.</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="text-secondary-fixed">✅</span>
                        <span>Protein hewani sangat penting untuk pertumbuhan tulang dan massa otot di masa
                            emas.</span>
                    </li>
                    <li class="flex gap-3">
                        <span class="text-secondary-fixed">✅</span>
                        <span>Susu UHT mengandung kalsium dan vitamin D yang mendukung pertumbuhan tinggi
                            badan.</span>
                    </li>
                </ul>
            </div>
            <div class="bg-surface-container-high p-6 rounded-2xl">
                <h3 class="text-sm font-bold mb-4">Target Capaian Nasional</h3>
                <div class="space-y-5">
                    <div>
                        <div class="flex justify-between text-[10px] font-bold mb-2">
                            <span>REDUKSI STUNTING</span>
                            <span class="text-primary">82%</span>
                        </div>
                        <div class="w-full bg-surface-container-lowest h-2.5 rounded-full overflow-hidden">
                            <div class="bg-primary h-full" style="width: 82%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-[10px] font-bold mb-2">
                            <span>PEMENUHAN ZAT BESI</span>
                            <span class="text-secondary">94%</span>
                        </div>
                        <div class="w-full bg-surface-container-lowest h-2.5 rounded-full overflow-hidden">
                            <div class="bg-secondary h-full" style="width: 94%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="mb-20">

        <div class="flex items-center justify-between mb-8">
            <h2 class="text-xl font-bold flex items-center gap-3">
                <span class="w-1.5 h-6 bg-primary rounded-full"></span>
                Jadwal Menu Mingguan
            </h2>
            <span class="text-xs bg-surface-container-high px-4 py-1.5 rounded-full text-on-surface-variant font-semibold">
               Senin - Jumat
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-5">

            @forelse($weeklyMenus as $menu)

                <div class="group {{ optional($latestMenu)->id == $menu->id ? 'bg-primary text-white' : 'bg-white' }} rounded-2xl p-5 shadow-xl transition-all hover:-translate-y-1.5 relative overflow-hidden">

                    @if(optional($latestMenu)->id == $menu->id)
                        <div class="absolute top-3 right-3 z-10 px-2.5 py-0.5 bg-white text-primary rounded-full text-[9px] font-black uppercase tracking-wider">
                            Terbaru
                        </div>
                    @endif

                    <div class="w-full aspect-[4/3] rounded-xl overflow-hidden mb-4">
                        <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                            src="{{ getPicture($menu->foto, '', $menu->updated_by) }}" />
                    </div>

                    <span class="text-xs font-bold opacity-80 mb-1.5 block uppercase">
                        {{ \Carbon\Carbon::parse($menu->tanggal)->translatedFormat('l') }}
                    </span>

                    <h3 class="text-lg font-headline font-semibold mb-3 leading-snug">
                        {{ $menu->nama }}
                    </h3>

                    <div class="space-y-1.5 pt-3 border-t border-white/20">
                        <div class="flex justify-between items-center text-xs">
                            <span class="opacity-80">Kalori (Avg)</span>
                            <span class="font-bold">
                                {{ ($menu->kecil_energi + $menu->besar_energi) / 2 }} kkal
                            </span>
                        </div>
                    </div>

                </div>

            @empty
            <p class="col-span-5 text-center text-gray-500">
                Data menu belum tersedia
            </p>
            @endforelse

        </div>

    </section>
</section>
@endsection