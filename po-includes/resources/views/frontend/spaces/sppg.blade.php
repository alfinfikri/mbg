@extends(getTheme('layouts.app'))

@section('content')
<section class="pt-36 pb-20 px-6 max-w-7xl mx-auto">
    <div class="mb-12 max-w-3xl">
        <h1 class="text-5xl font-headline font-bold text-on-surface leading-tight">
            Daftar Dapur
            <span class="text-primary italic">SPPG MBG</span>
        </h1>
        <p class="mt-6 text-body-lg text-on-surface-variant leading-relaxed">
            Transparansi data dapur Satuan Pelayanan Pemenuhan Gizi, jejaring sekolah yang dilayani, dan kapasitas penerima manfaat.
        </p>
    </div>

    <div class="grid md:grid-cols-4 gap-6 mb-16">
        <div class="bg-primary text-on-primary p-8 rounded-xl shadow-xl min-h-[180px]">
            <span class="material-symbols-outlined text-3xl mb-4 opacity-80">soup_kitchen</span>
            <div class="text-3xl font-bold">{{ number_format($totalSppg, 0, ',', '.') }}</div>
            <p class="text-sm mt-2 opacity-80">Total Unit SPPG</p>
        </div>
        <div class="bg-surface-container-lowest p-8 rounded-xl shadow-sm border-l-4 border-secondary">
            <span class="material-symbols-outlined text-secondary mb-4">verified</span>
            <div class="text-3xl font-bold">{{ number_format($totalSppgAktif, 0, ',', '.') }}</div>
            <p class="text-sm text-on-surface-variant mt-2">SPPG Aktif</p>
        </div>
        <div class="bg-surface-container-lowest p-8 rounded-xl shadow-sm border-l-4 border-tertiary">
            <span class="material-symbols-outlined text-tertiary mb-4">school</span>
            <div class="text-3xl font-bold">{{ number_format($totalSekolahDilayani, 0, ',', '.') }}</div>
            <p class="text-sm text-on-surface-variant mt-2">Sekolah Dilayani</p>
        </div>
        <div class="bg-surface-container-lowest p-8 rounded-xl shadow-sm border-l-4 border-primary-container">
            <span class="material-symbols-outlined text-primary-container mb-4">groups</span>
            <div class="text-3xl font-bold">{{ number_format($totalPenerima, 0, ',', '.') }}</div>
            <p class="text-sm text-on-surface-variant mt-2">Total Penerima</p>
        </div>
    </div>

    <section class="bg-surface-container-lowest rounded-xl shadow-sm overflow-hidden mb-16">
        <form method="GET" action="{{ route('sppg') }}" class="p-8 flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-surface-container-low">
            <div>
                <h2 class="text-2xl font-headline font-bold text-on-surface">Rincian Data SPPG</h2>
                <p class="text-sm text-on-surface-variant mt-1">Gunakan filter untuk mempersempit pencarian data.</p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <div class="relative">
                    <select name="status_layanan" onchange="this.form.submit()"
                        class="appearance-none bg-surface-container-low border-none rounded-xl pl-4 pr-10 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary/20 min-w-[180px]">
                        <option value="">Semua Status</option>
                        <option value="1" {{ request('status_layanan') === '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="2" {{ request('status_layanan') === '2' ? 'selected' : '' }}>Tidak Aktif</option>
                        <option value="3" {{ request('status_layanan') === '3' ? 'selected' : '' }}>Belum Operasi</option>
                    </select>
                    <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-on-surface-variant">expand_more</span>
                </div>

                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari nama, alamat, penanggung jawab..."
                    class="bg-surface-container-low rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary/20 min-w-[240px]" />

                <button type="submit" class="bg-primary text-on-primary px-6 py-2.5 rounded-xl font-bold text-sm inline-flex items-center gap-2">
                    <span class="material-symbols-outlined text-base">search</span>
                </button>

                @if(request()->hasAny(['search', 'status_layanan']))
                    <a href="{{ route('sppg') }}" class="bg-surface-container-low text-primary px-5 py-2.5 rounded-xl font-bold text-sm">Reset</a>
                @endif
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-low text-on-surface-variant uppercase text-[10px] tracking-widest font-bold">
                        <th class="px-8 py-5">Nama SPPG</th>
                        <th class="px-6 py-5">Wilayah</th>
                        <th class="px-6 py-5">Penanggung Jawab</th>
                        <th class="px-6 py-5">Status</th>
                        <th class="px-6 py-5">Sekolah</th>
                        <th class="px-6 py-5">Penerima</th>
                        <th class="px-8 py-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-container-low">
                    @forelse($sppgs as $sppg)
                        @php
                            $status = [
                                1 => ['Aktif', 'bg-green-100 text-green-800'],
                                2 => ['Tidak Aktif', 'bg-yellow-100 text-yellow-800'],
                                3 => ['Belum Operasi', 'bg-red-100 text-red-800'],
                            ][$sppg->status_layanan] ?? ['-', 'bg-surface-container text-on-surface'];
                        @endphp
                        <tr class="hover:bg-surface-container-low/50 transition-colors">
                            <td class="px-8 py-6">
                                <div class="flex flex-col">
                                    <span class="font-bold text-on-surface">{{ $sppg->nama }}</span>
                                    <span class="text-xs text-on-surface-variant mt-1">{{ $sppg->alamat ?: '-' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-6">
                                <div class="flex flex-col text-sm">
                                    <span class="text-on-surface">{{ optional(optional($sppg->wilayah)->parent)->nama_wilayah ?? '-' }}</span>
                                    <span class="text-xs text-on-surface-variant">{{ optional($sppg->wilayah)->nama_wilayah ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-6">
                                <div class="flex flex-col text-sm">
                                    <span class="font-semibold text-on-surface">{{ $sppg->nama_penanggung_jawab ?: '-' }}</span>
                                    <span class="text-xs text-on-surface-variant">{{ $sppg->no_hp ?: ($sppg->email ?: '-') }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-6">
                                <span class="px-2 py-1 {{ $status[1] }} text-xs rounded-full">{{ $status[0] }}</span>
                            </td>
                            <td class="px-6 py-6 font-semibold text-on-surface">
                                {{ number_format($sppg->sekolahs_count, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-6 font-semibold text-on-surface">
                                {{ number_format($sppg->sekolahs->sum('jumlah_total'), 0, ',', '.') }}
                            </td>
                            <td class="px-8 py-6 text-right">
                                <a href="{{ route('sppg.detail', Hashids::encode($sppg->id)) }}"
                                    class="text-primary font-bold text-sm hover:underline">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center p-5 text-gray-500">
                                Data SPPG belum tersedia
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-8 flex flex-col md:flex-row md:items-center justify-between gap-4 border-t border-surface-container-low">
            <div class="text-sm text-on-surface-variant">
                @if($sppgs->total() > 0)
                    Menampilkan
                    <span class="font-bold text-on-surface">{{ $sppgs->firstItem() }} - {{ $sppgs->lastItem() }}</span>
                    dari
                    <span class="font-bold text-on-surface">{{ $sppgs->total() }}</span> SPPG
                @else
                    Tidak ada data SPPG
                @endif
            </div>

            <div class="flex items-center gap-2">
                @if ($sppgs->onFirstPage())
                    <span class="w-10 h-10 flex items-center justify-center rounded-xl border opacity-50">&lsaquo;</span>
                @else
                    <a href="{{ $sppgs->previousPageUrl() }}" class="w-10 h-10 flex items-center justify-center rounded-xl border hover:bg-surface-container-low">&lsaquo;</a>
                @endif

                @for ($i = 1; $i <= $sppgs->lastPage(); $i++)
                    <a href="{{ $sppgs->url($i) }}"
                        class="w-10 h-10 flex items-center justify-center rounded-xl {{ $sppgs->currentPage() == $i ? 'bg-primary text-white' : 'hover:bg-surface-container-low' }}">
                        {{ $i }}
                    </a>
                @endfor

                @if ($sppgs->hasMorePages())
                    <a href="{{ $sppgs->nextPageUrl() }}" class="w-10 h-10 flex items-center justify-center rounded-xl border hover:bg-surface-container-low">&rsaquo;</a>
                @else
                    <span class="w-10 h-10 flex items-center justify-center rounded-xl border opacity-50">&rsaquo;</span>
                @endif
            </div>
        </div>
    </section>
</section>
@endsection
