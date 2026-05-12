@extends(getTheme('layouts.app'))

@section('content')
@php
    $queryBase = ['tanggal' => $tanggal, 'jenis' => $jenis, 'search' => $search];
    $statusTabs = [
        'sudah' => 'Sudah Lapor',
        'belum' => 'Belum Lapor',
        'rekap' => 'Rekap Update',
    ];
    $jenisTabs = [
        'sekolah' => 'Sekolah',
        'sppg' => 'SPPG',
    ];
@endphp

<section class="pt-32 pb-16 px-6 bg-surface">
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6 mb-8">
            <div>
                <p class="text-xs uppercase tracking-widest text-outline font-bold mb-3">MONITORING HARIAN</p>
                <h1 class="text-4xl md:text-5xl font-bold font-headline text-primary">Detail Laporan Harian</h1>
                <p class="text-on-surface-variant mt-3">Menampilkan SPPG dan sekolah yang sudah atau belum membuat laporan pada {{ $tanggalLabel }}.</p>
            </div>
            <a href="{{ url('/') }}" class="inline-flex items-center gap-2 text-primary font-bold">
                <span class="material-symbols-outlined text-base">arrow_back</span>
                Kembali ke Home
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-surface-container-low p-4 md:p-5 mb-6">
            <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-5">
                <div class="flex flex-wrap gap-2">
                    @foreach($statusTabs as $key => $label)
                        <a href="{{ url('/laporan-harian?'.http_build_query(array_merge($queryBase, ['status' => $key]))) }}"
                           class="px-4 py-2 rounded-full text-sm font-bold transition-colors {{ $status === $key ? 'bg-primary text-white shadow-sm' : 'bg-surface-container-low text-on-surface-variant hover:bg-primary-fixed hover:text-primary' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>

                <div class="flex flex-wrap gap-2">
                    @foreach($jenisTabs as $key => $label)
                        <a href="{{ url('/laporan-harian?'.http_build_query(['tanggal' => $tanggal, 'jenis' => $key, 'status' => $status, 'search' => $search])) }}"
                           class="px-4 py-2 rounded-xl text-sm font-bold border transition-colors {{ $jenis === $key ? 'bg-secondary text-white border-secondary' : 'bg-white text-primary border-outline-variant hover:bg-secondary-container' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>

            <form method="GET" action="{{ url('/laporan-harian') }}" class="grid md:grid-cols-12 gap-3 mt-5">
                <input type="hidden" name="jenis" value="{{ $jenis }}">
                <input type="hidden" name="status" value="{{ $status }}">
                <div class="md:col-span-3">
                    <label class="text-xs font-bold uppercase tracking-widest text-outline">Tanggal</label>
                    <input type="date" name="tanggal" value="{{ $tanggal }}" class="mt-2 w-full rounded-xl border-outline-variant bg-surface-container-lowest focus:ring-primary focus:border-primary">
                </div>
                <div class="md:col-span-6">
                    <label class="text-xs font-bold uppercase tracking-widest text-outline">Cari nama atau lokasi</label>
                    <input type="text" name="search" value="{{ $search }}" placeholder="{{ $jenis === 'sekolah' ? 'Nama sekolah, SPPG, wilayah' : 'Nama SPPG, wilayah, alamat' }}" class="mt-2 w-full rounded-xl border-outline-variant bg-surface-container-lowest focus:ring-primary focus:border-primary">
                </div>
                <div class="md:col-span-3 flex items-end gap-2">
                    <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 rounded-xl bg-primary text-white font-bold px-4 py-3 hover:bg-primary-container transition-colors">
                        <span class="material-symbols-outlined text-base">search</span>
                        Cari
                    </button>
                    <a href="{{ url('/laporan-harian?'.http_build_query(['jenis' => $jenis, 'status' => $status])) }}" class="inline-flex items-center justify-center rounded-xl border border-outline-variant text-primary font-bold px-4 py-3 hover:bg-primary-fixed transition-colors">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <div class="grid lg:grid-cols-3 gap-5 mb-6">
            <div class="lg:col-span-2 bg-primary text-white rounded-2xl p-6 shadow-sm">
                <p class="text-sm text-primary-fixed">{{ $jenis === 'sekolah' ? 'Total Data Sekolah' : 'Total Data SPPG' }}</p>
                <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mt-2">
                    <p class="text-5xl font-black">{{ number_format($totalTarget, 0, ',', '.') }}</p>
                    <div class="grid grid-cols-2 gap-3 min-w-[260px]">
                        <div class="bg-white/20 rounded-xl p-3">
                            <p class="text-xs text-white/70">Sudah Lapor</p>
                            <p class="text-2xl font-extrabold">{{ number_format($totalSudahLapor, 0, ',', '.') }}</p>
                        </div>
                        <div class="bg-white/20 rounded-xl p-3">
                            <p class="text-xs text-white/70">Belum Lapor</p>
                            <p class="text-2xl font-extrabold">{{ number_format($totalBelumLapor, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-surface-container-low">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-sm text-on-surface-variant">Data Ditampilkan</p>
                        <p class="text-4xl font-extrabold text-secondary mt-2">{{ number_format($rows->total(), 0, ',', '.') }}</p>
                    </div>
                    <span class="material-symbols-outlined text-secondary bg-secondary-container rounded-2xl p-3">fact_check</span>
                </div>
                <p class="text-xs text-on-surface-variant mt-4">Hasil mengikuti tab, jenis data, tanggal, dan kata kunci aktif.</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-surface-container-low overflow-hidden">
            @include('frontend.laporan-harian.partials.table')
        </div>
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
