@extends(getTheme('layouts.app'))

@section('content')

<section class="py-12 max-w-7xl mx-auto px-6">

    <section class="py-12 max-w-7xl mx-auto px-6">

    <!-- HEADER FULL WIDTH -->
    <div class="mb-12 max-w-3xl">
        <h1 class="text-5xl font-headline font-bold text-on-surface leading-tight">
            Daftar Sekolah Penerima 
            <span class="text-primary italic">Manfaat MBG</span>
        </h1>

        <p class="mt-6 text-body-lg text-on-surface-variant leading-relaxed">
            Transparansi data distribusi pangan bergizi untuk satuan pendidikan di Wilayah Kota Serang.
            Memantau setiap kalori untuk masa depan bangsa.
        </p>
    </div>

    <!-- STATISTICS (HORIZONTAL) -->
    <div class="grid md:grid-cols-3 gap-6 mb-16">

        <!-- CARD 1 -->

        <div class="bg-primary text-on-primary p-8 rounded-xl shadow-xl min-h-[220px]">
            <span class="material-symbols-outlined text-3xl mb-4 opacity-80">groups</span>

            <div class="text-3xl font-bold">
                {{ number_format($totalSiswaMbg, 0, ',', '.') }} 
                / 
                {{ number_format($totalSiswa, 0, ',', '.') }}
            </div>

            <p class="text-sm mt-2 opacity-80">Total Penerima Manfaat MBG</p>

            <div class="text-xl font-semibold mt-2">
                {{ $persenSiswa }}%
            </div>

            <p class="text-xs opacity-70">Pencapaian</p>

            <div class="mt-4">
                <div class="w-full bg-white/20 h-2 rounded-full">
                    <div class="bg-white h-2 rounded-full" style="width: {{ $persenSiswa }}%"></div>
                </div>
            </div>
        </div>

        <!-- CARD 2 -->
        <div class="bg-surface-container-lowest p-8 rounded-xl shadow-sm border-l-4 border-secondary">
            <span class="material-symbols-outlined text-secondary mb-4">school</span>

            <div class="text-3xl font-bold">
                {{ number_format($sekolahMbg) }}
            </div>

            <div class="text-sm text-gray-500">
                dari {{ number_format($totalSekolah) }} Sekolah
            </div>

            <p class="text-xs mt-2">PAUD - SMA sudah menerima manfaat</p>

            <div class="text-lg font-semibold mt-2 text-secondary">
                {{ $persenSekolah }}%
            </div>

            <div class="mt-4">
                <div class="w-full bg-gray-200 h-2 rounded-full">
                    <div class="bg-secondary h-2 rounded-full" style="width: {{ $persenSekolah }}%"></div>
                </div>
            </div>
        </div>

        <!-- CARD 3 -->
        <div class="bg-surface-container-lowest p-8 rounded-xl shadow-sm border-l-4 border-tertiary">
            <span class="material-symbols-outlined text-tertiary mb-4">pregnant_woman</span>

            <div class="text-3xl font-bold">
                {{ number_format($totalIbuBalitaMbg) }}
            </div>

            <div class="text-sm text-gray-500">
                dari {{ number_format($totalIbuBalita) }} Ibu & Balita
            </div>

            <p class="text-xs mt-2">Bumil, Busui & Balita sudah menerima manfaat</p>

            <div class="text-lg font-semibold mt-2 text-tertiary">
                {{ $persenIbuBalita }}%
            </div>

            <div class="mt-4">
                <div class="w-full bg-gray-200 h-2 rounded-full">
                    <div class="bg-tertiary h-2 rounded-full" style="width: {{ $persenIbuBalita }}%"></div>
                </div>
            </div>
        </div>

    </div>
    <!-- DataTable Section -->
    <section class="bg-surface-container-lowest rounded-xl shadow-sm overflow-hidden mb-16">
        <!-- Table Header/Filters -->
        <form method="GET" action="" class="p-8 flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-surface-container-low">

            <div>
                <h2 class="text-2xl font-headline font-bold text-on-surface">Rincian Data Sekolah</h2>
                <p class="text-sm text-on-surface-variant mt-1">Gunakan filter untuk mempersempit pencarian data.</p>
            </div>

            <div class="flex flex-wrap items-center gap-3">

                <!-- 🏙️ KECAMATAN -->
                <div class="relative">
                    <select name="kecamatan" id="kecamatan" onchange="this.form.submit()"
                        class="appearance-none bg-surface-container-low border-none rounded-xl pl-4 pr-10 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary/20 min-w-[160px]">>
                        <option value="">Semua Kecamatan</option>
                        @foreach($kecamatans as $id => $nama)
                            <option value="{{ $id }}" {{ request('kecamatan') == $id ? 'selected' : '' }}>{{ $nama }}</option>
                        @endforeach
                    </select>            
                    <span
                        class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-on-surface-variant">expand_more</span>
                </div>

                <!-- 🏘️ KELURAHAN -->
                <div class="relative">
                    <select name="kelurahan" id="kelurahan" onchange="this.form.submit()"
                        class="appearance-none bg-surface-container-low border-none rounded-xl pl-4 pr-10 py-2.5 text-sm font-medium focus:ring-2 focus:ring-primary/20 min-w-[160px]">>
                        <option value="">Semua Kelurahan</option>
                    </select>
                    <span
                        class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-on-surface-variant">expand_more</span>
                </div>

                <!-- 🔍 SEARCH -->
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari nama sekolah..."
                    class="bg-surface-container-low rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary/20" />

                <!-- 🔘 SUBMIT -->
                <button type="submit"
                    class="bg-primary text-on-primary px-6 py-2.5 rounded-xl font-bold text-sm">
                    <span class="material-symbols-outlined text-base">search</span>
                </button>

            </div>
        </form>
        <!-- The Table -->
        <div class="overflow-x-auto">
            <table id="table-sekolah" class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-low text-on-surface-variant uppercase text-[10px] tracking-widest font-bold">
                        <th class="px-8 py-5">Nama Sekolah &amp; Alamat</th>
                        <th class="px-6 py-5">Lokasi</th>
                        <th class="px-6 py-5">Jumlah Siswa</th>
                        <th class="px-6 py-5">Status Layanan</th>
                        <th class="px-6 py-5">SPPG</th>
                        <th class="px-8 py-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-container-low">
                    @forelse($sekolahs as $sekolah)
                    <tr class="hover:bg-surface-container-low/50 transition-colors">

                        <!-- Nama -->
                        <td class="px-8 py-6">
                            <div class="flex flex-col">
                                <span class="font-bold text-on-surface">{{ $sekolah->nama }}</span>
                                <span class="text-xs text-on-surface-variant">{{ $sekolah->alamat }}</span>
                            </div>
                        </td>

                        <!-- Lokasi -->
                        <td class="px-6 py-6">
                            <div class="flex flex-col text-sm">
                                <span class="text-on-surface">{{ optional($sekolah->wilayah->parent)->nama_wilayah ?? '-' }}</span>
                                <span class="text-xs text-on-surface-variant">{{ $sekolah->wilayah->nama_wilayah ?? '-' }}</span>
                            </div>
                        </td>

                        <!-- Jumlah -->
                        <td class="px-6 py-6 font-semibold text-on-surface">
                            {{ $sekolah->jumlah_total }} Siswa
                        </td>

                        <!-- Status -->
                        <td class="px-6 py-6">
                            @if($sekolah->status_layanan == 1)
                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Aktif</span>
                            @elseif($sekolah->status_layanan == 2)
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full">Tidak Aktif</span>
                            @elseif($sekolah->status_layanan == 3)
                                <span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full">Menolak</span>
                            @endif
                        </td>

                        <!-- SPPG -->
                        <td class="px-6 py-6 text-sm italic text-on-surface-variant">
                            {{ $sekolah->sppgs->pluck('nama')->implode(', ') ?: '-' }}
                        </td>

                        <!-- Aksi -->
                        <td class="px-8 py-6 text-right">
                            <a href="{{ route('sekolah.detail', Hashids::encode($sekolah->id)) }}" 
                            class="text-primary font-bold text-sm hover:underline">
                                Detail
                            </a>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center p-5 text-gray-500">
                            Data sekolah belum tersedia
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Server-Side Pagination -->
       <div class="p-8 flex items-center justify-between border-t border-surface-container-low">

            <div class="text-sm text-on-surface-variant">
                Menampilkan 
                <span class="font-bold text-on-surface">
                    {{ $sekolahs->firstItem() }} - {{ $sekolahs->lastItem() }}
                </span> 
                dari 
                <span class="font-bold text-on-surface">
                    {{ $sekolahs->total() }}
                </span> sekolah
            </div>

            <div class="flex items-center gap-2">

                {{-- Prev --}}
                @if ($sekolahs->onFirstPage())
                    <span class="w-10 h-10 flex items-center justify-center rounded-xl border opacity-50">
                        ‹
                    </span>
                @else
                    <a href="{{ $sekolahs->previousPageUrl() }}" 
                    class="w-10 h-10 flex items-center justify-center rounded-xl border hover:bg-surface-container-low">
                        ‹
                    </a>
                @endif

                {{-- Pages --}}
                @for ($i = 1; $i <= $sekolahs->lastPage(); $i++)
                    <a href="{{ $sekolahs->url($i) }}"
                    class="w-10 h-10 flex items-center justify-center rounded-xl 
                    {{ $sekolahs->currentPage() == $i ? 'bg-primary text-white' : 'hover:bg-surface-container-low' }}">
                        {{ $i }}
                    </a>
                @endfor

                {{-- Next --}}
                @if ($sekolahs->hasMorePages())
                    <a href="{{ $sekolahs->nextPageUrl() }}" 
                    class="w-10 h-10 flex items-center justify-center rounded-xl border hover:bg-surface-container-low">
                        ›
                    </a>
                @else
                    <span class="w-10 h-10 flex items-center justify-center rounded-xl border opacity-50">
                        ›
                    </span>
                @endif

            </div>
        </div>
    </section>
</section>
@endsection
@push('scripts')
<script src="{{ asset('po-admin/lib/jquery/jquery.min.js') }}"></script>
<script>
    $('#kecamatan').on('change', function () {
        let id = $(this).val();

        $('#kelurahan').html('<option>Loading...</option>');

        if (id) {
            $.get('/get-kelurahan/' + id, function (data) {
                let options = '<option value="">Semua Kelurahan</option>';

                $.each(data, function (id, nama) {
                    options += `<option value="${id}">${nama}</option>`;
                });

                $('#kelurahan').html(options);
            }).fail(function () {
                $('#kelurahan').html('<option value="">Gagal load</option>');
            });
        } else {
            $('#kelurahan').html('<option value="">Semua Kelurahan</option>');
        }
    });

    document.addEventListener("DOMContentLoaded", function() {
        const links = document.querySelectorAll(".menu-link");

        links.forEach(link => {
            link.addEventListener("click", function() {

                // reset semua
                links.forEach(l => {
                    l.classList.remove("text-blue-700", "font-bold", "bg-white", "shadow-sm");
                    l.classList.add("text-slate-500");
                });

                // aktifkan yang diklik
                this.classList.remove("text-slate-500");
                this.classList.add("text-blue-700", "font-bold", "bg-white", "shadow-sm");
            });
        });
    });
</script>
@endpush