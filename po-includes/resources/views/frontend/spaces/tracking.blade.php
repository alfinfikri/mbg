@extends(getTheme('layouts.app'))

@section('content')

<section class="pt-32 pb-20 px-6 max-w-7xl mx-auto">
	<!-- Hero / Search Section -->
	<section class="mb-12">
		<div class="relative overflow-hidden rounded-[2rem] bg-gradient-to-br from-primary to-primary-container p-12 md:p-16 text-on-primary shadow-xl">
			<div class="absolute top-0 right-0 w-1/3 h-full opacity-10 pointer-events-none">
				<div class="w-full h-full bg-[radial-gradient(circle,white_1px,transparent_1px)] [background-size:20px_20px]"></div>
			</div>
			<div class="relative z-10 max-w-2xl">
				<h1 class="text-4xl md:text-5xl font-bold tracking-tight mb-6">Lacak Status Aduan Anda</h1>
				<p class="text-on-primary-container text-lg mb-10 opacity-90 leading-relaxed">
					Pantau progres penanganan laporan Anda secara real-time. Masukkan Kode tiket yang Anda dapatkan saat melakukan pengaduan.
				</p>
				<form method="POST" action="{{ url('/lacak-pengaduan') }}" class="flex flex-col md:flex-row gap-4">
					@csrf
					<div class="relative flex-grow">
						<span class="absolute left-4 top-1/2 -translate-y-1/2 text-outline-variant material-symbols-outlined" data-icon="confirmation_number">confirmation_number</span>
						<input name="kode_tiket" value="{{ request('kode_tiket') }}" class="w-full pl-12 pr-4 py-4 rounded-xl bg-surface-container-lowest text-on-surface border-none focus:ring-2 focus:ring-secondary-container shadow-sm transition-all placeholder:text-outline" placeholder="Kode Tiket (Contoh: MBG-20260428-7F3A)" type="text" />
					</div>
					<button class="bg-secondary-container text-on-secondary-container px-10 py-4 rounded-xl font-bold hover:shadow-lg transition-all active:scale-95">
						Lacak Aduan
					</button>
				</form>
			</div>
		</div>
	</section>
	@if(request('kode_tiket') && !$aduan)
		<div class="bg-red-50 text-red-700 rounded-2xl p-6 mb-8">
			Kode pengaduan tidak ditemukan.
		</div>
	@endif
	@if($aduan)
	@php
		$assignedSatgasUsers = $aduan->disposisiSatgasUsers();
		$satgasResponses = $aduan->respon_satgas ?: [];
		$sppgName = optional($aduan->disposisiSppg)->nama ?? optional($aduan->sppg)->nama ?? '-';
	@endphp
	<!-- Main Content Grid -->
	<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
		<!-- Top Row: Timeline -->
		<div class="lg:col-span-12">
			<!-- Tracking Timeline Card -->
			<div class="bg-surface-container-lowest rounded-3xl p-8 shadow-[0px_12px_32px_rgba(24,28,30,0.06)]">
				<div class="flex items-center justify-between mb-10">
					<h2 class="text-xl font-semibold flex items-center gap-2">
						<span class="material-symbols-outlined text-primary">route</span>
						Status Penanganan
					</h2>

					@php
						$statusText = [
							'0' => 'Aduan diterima',
							'1' => 'Aduan sudah didisposisikan',
							'2' => 'Aduan sedang diproses',
							'3' => 'Aduan selesai',
							'4' => 'Aduan ditolak / tidak dapat diproses',
						];

						$status = $aduan->status_pengaduan ?? $aduan->status ?? 0;

						$stepClass = function($target) use ($status) {
							if ($status > $target) return 'bg-secondary text-on-secondary';
							if ($status == $target) return 'bg-primary text-on-primary ring-4 ring-primary-fixed';
							return 'bg-surface-container-highest text-outline opacity-40';
						};

						$textClass = function($target) use ($status) {
							return $status >= $target ? 'text-on-surface font-bold' : 'text-on-surface opacity-40';
						};
					@endphp

					<span class="bg-secondary-container/30 text-on-secondary-container px-4 py-1.5 rounded-full text-sm font-bold flex items-center gap-2">
						<span class="relative flex h-2 w-2">
							<span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-secondary opacity-75"></span>
							<span class="relative inline-flex rounded-full h-2 w-2 bg-secondary"></span>
						</span>
						{{ $aduan ? $statusText[$aduan->status_pengaduan ?? $aduan->status] ?? '-' : '-' }}
					</span>
				</div>

				<div class="flex flex-col md:flex-row justify-between gap-8 relative">

					{{-- STEP 1 --}}
					<div class="flex md:flex-col items-start md:items-center flex-1 group">
						<div class="w-12 h-12 rounded-full flex items-center justify-center {{ $stepClass(0) }}">
							<span class="material-symbols-outlined">check</span>
						</div>
						<div class="ml-4 md:ml-0 md:mt-4">
							<h3 class="{{ $textClass(0) }}">Laporan Diterima</h3>
							<p class="text-xs text-outline-variant mt-1">
								{{ $aduan ? \Carbon\Carbon::parse($aduan->tgl_aduan)->format('d M Y') : '-' }}
							</p>
						</div>
					</div>

					{{-- STEP 2 --}}
					<div class="flex md:flex-col items-start md:items-center flex-1 group">
						<div class="w-12 h-12 rounded-full flex items-center justify-center {{ $stepClass(1) }}">
							<span class="material-symbols-outlined">task_alt</span>
						</div>
						<div class="ml-4 md:ml-0 md:mt-4">
							<h3 class="{{ $textClass(1) }}">Verifikasi</h3>
							<p class="text-xs text-outline-variant mt-1">
								{{ $aduan->disposisi_at || $aduan->tgl_disposisi ? \Carbon\Carbon::parse($aduan->disposisi_at ?: $aduan->tgl_disposisi)->format('d M Y') : '-' }}
							</p>
						</div>
					</div>

					{{-- STEP 3 --}}
					<div class="flex md:flex-col items-start md:items-center flex-1 group">
						<div class="w-12 h-12 rounded-full flex items-center justify-center {{ $stepClass(2) }}">
							<span class="material-symbols-outlined">settings_suggest</span>
						</div>
						<div class="ml-4 md:ml-0 md:mt-4">
							<h3 class="{{ $textClass(2) }}">Diproses</h3>
							<p class="text-xs text-primary-container mt-1">
								{{ $status >= 2 ? 'Estimasi: 1-2 Hari Kerja' : '-' }}
							</p>
						</div>
					</div>

					{{-- STEP 4 --}}
					<div class="flex md:flex-col items-start md:items-center flex-1 group">
						<div class="w-12 h-12 rounded-full flex items-center justify-center {{ $stepClass(3) }}">
							<span class="material-symbols-outlined">flag</span>
						</div>
						<div class="ml-4 md:ml-0 md:mt-4">
							<h3 class="{{ $textClass(2) }}">Selesai</h3>
							<p class="text-xs text-outline mt-1">
								{{ $status == 3 && ($aduan->closed_at || $aduan->tgl_selesai) ? \Carbon\Carbon::parse($aduan->closed_at ?: $aduan->tgl_selesai)->format('d M Y') : 'Belum tercapai' }}
							</p>
						</div>
					</div>

				</div>
			</div>
		</div>
		<!-- Left Column: Details & Responses -->
		<div class="lg:col-span-8 space-y-8">
			<!-- Detail Card -->
			<div class="bg-surface-container-lowest rounded-3xl overflow-hidden shadow-[0px_12px_32px_rgba(24,28,30,0.06)]">
				<div class="bg-primary-container p-6 text-on-primary">
					<p class="text-xs font-medium opacity-80 mb-1">Kode Tiket</p>
					<h2 class="text-2xl font-bold tracking-tight">{{ $aduan->kode_tiket ?? '-' }}</h2>
				</div>
				<div class="p-8 space-y-6">
					<div class="grid grid-cols-2 gap-y-6">
						<div>
							<p class="text-[10px] uppercase tracking-widest text-outline font-bold mb-1">Tanggal</p>
							<p class="text-sm font-semibold text-on-surface">{{ $aduan ? \Carbon\Carbon::parse($aduan->tgl_aduan)->format('d F Y') : '-' }}</p>
						</div>
						<div>
							<p class="text-[10px] uppercase tracking-widest text-outline font-bold mb-1">Lokasi Kejadian</p>
							<p class="text-sm font-semibold text-on-surface">{{ $aduan->alamat ?? '-' }}</p>
						</div>
						<div class="col-span-2">
							<p class="text-[10px] uppercase tracking-widest text-outline font-bold mb-1">Judul Aduan</p>
							<p class="text-sm font-semibold text-on-surface">{{ $aduan->judul_aduan ?? '-' }}</p>
						</div>
					</div>
					<div class="pt-6 border-t border-surface-container-high">
						<p class="text-[10px] uppercase tracking-widest text-outline font-bold mb-2">Deskripsi</p>
						<p class="text-sm text-on-surface-variant leading-relaxed italic">
							{{ $aduan->isi_aduan ?? '-' }}
						</p>
					</div>
					@if($assignedSatgasUsers->count())
					<div class="pt-6 border-t border-surface-container-high">
						<p class="text-[10px] uppercase tracking-widest text-outline font-bold mb-3">Satgas Penanganan</p>
						<div class="flex flex-wrap gap-2">
							@foreach($assignedSatgasUsers as $satgas)
								<span class="px-3 py-1 rounded-full bg-primary-fixed text-primary text-xs font-bold">{{ $satgas->name }}</span>
							@endforeach
						</div>
					</div>
					@endif
					@if(!empty($aduan->foto))
					<div class="pt-6">
						<p class="text-[10px] uppercase tracking-widest text-outline font-bold mb-3">
							Lampiran
						</p>

						<div class="flex gap-2">

							<a href="{{ asset('po-content/uploads/'.$aduan->foto) }}" data-tracking-photo data-photo-title="Lampiran aduan - {{ $aduan->kode_tiket }}">
								<div class="w-20 h-20 rounded-lg overflow-hidden border">
									<img 
										src="{{ asset('po-content/uploads/'.$aduan->foto) }}"
										alt="Lampiran"
										class="w-full h-full object-cover hover:scale-105 transition-all"
									/>
								</div>
							</a>

						</div>
					</div>
					@endif
					@if(!empty($satgasResponses))
					<div class="pt-6 border-t border-surface-container-high">
						<p class="text-[10px] uppercase tracking-widest text-outline font-bold mb-3">Tanggapan Resmi Satgas</p>
						<div class="space-y-4">
							@foreach($satgasResponses as $response)
								<div class="rounded-2xl bg-surface-container-low p-4">
									<div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between mb-2">
										<p class="text-sm font-bold text-primary">{{ $response['name'] ?? 'Satgas' }}</p>
										@if(!empty($response['responded_at']))
											<p class="text-[10px] text-outline">{{ \Carbon\Carbon::parse($response['responded_at'])->format('d M Y, H:i') }} WIB</p>
										@endif
									</div>
									<div class="text-sm text-on-surface-variant leading-relaxed">{!! $response['tanggapan'] ?? '-' !!}</div>
								</div>
							@endforeach
						</div>
					</div>
					@elseif(!empty($aduan->tanggapan))
					<div class="pt-6 border-t border-surface-container-high">
						<p class="text-[10px] uppercase tracking-widest text-outline font-bold mb-2">Tanggapan Resmi Satgas</p>
						<div class="text-sm text-on-surface-variant leading-relaxed">{!! $aduan->tanggapan !!}</div>
					</div>
					@endif
					@if(!empty($aduan->tanggapan_sppg))
					<div class="pt-6 border-t border-surface-container-high">
						<p class="text-[10px] uppercase tracking-widest text-outline font-bold mb-2">Tindak Lanjut SPPG</p>
						<p class="text-sm font-bold text-primary mb-2">{{ $sppgName }}</p>
						<div class="text-sm text-on-surface-variant leading-relaxed">{!! $aduan->tanggapan_sppg !!}</div>
					</div>
					@endif
					@if(!empty($aduan->foto_tindak_lanjut))
					<div class="pt-6">
						<p class="text-[10px] uppercase tracking-widest text-outline font-bold mb-3">
							Foto Tindak Lanjut
						</p>

						<a href="{{ asset('po-content/uploads/'.$aduan->foto_tindak_lanjut) }}" data-tracking-photo data-photo-title="Foto tindak lanjut - {{ $aduan->kode_tiket }}">
							<div class="w-20 h-20 rounded-lg overflow-hidden border">
								<img 
									src="{{ asset('po-content/uploads/'.$aduan->foto_tindak_lanjut) }}"
									alt="Foto tindak lanjut"
									class="w-full h-full object-cover hover:scale-105 transition-all"
								/>
							</div>
						</a>
					</div>
					@endif
				</div>
			</div>
			<!-- Help Card
			<div class="bg-tertiary-fixed rounded-3xl p-8 relative overflow-hidden group">
				<div class="absolute -right-4 -bottom-4 opacity-10 group-hover:scale-110 transition-transform duration-500">
					<span class="material-symbols-outlined text-9xl text-on-tertiary-fixed" data-icon="support_agent">support_agent</span>
				</div>
				<div class="relative z-10">
					<h3 class="text-on-tertiary-fixed font-bold text-lg mb-2">Butuh Bantuan Lebih Lanjut?</h3>
					<p class="text-on-tertiary-fixed-variant text-sm mb-6 leading-relaxed">
						Hubungi layanan pelanggan kami jika Anda memiliki kendala atau ingin memberikan informasi tambahan terkait aduan ini.
					</p>
					<button class="bg-on-tertiary-fixed text-tertiary-fixed px-6 py-2.5 rounded-full font-bold text-sm hover:shadow-lg transition-all">
						Chat Admin MBG
					</button>
				</div>
			</div> -->
		</div>

		<!-- Right Column: Activity Log -->
		<aside class="lg:col-span-4 space-y-8 lg:sticky lg:top-24 self-start">
			<div class="bg-surface-container-low rounded-3xl p-6 md:p-8">
				<h2 class="text-lg font-bold mb-6 text-on-surface border-l-4 border-primary pl-4">
					Log Aktivitas
				</h2>

				<div class="space-y-4">

					@forelse($logs as $log)

					@php
						$map = [
							'Laporan Diterima' => ['icon' => 'task_alt', 'bg' => 'bg-secondary-container text-on-secondary-container'],
							'Aduan Di Disposisikan' => ['icon' => 'info', 'bg' => 'bg-primary-fixed text-primary'],
							'Aduan Di Disposisikan ke Satgas' => ['icon' => 'info', 'bg' => 'bg-primary-fixed text-primary'],
							'Aduan Direspon oleh Satgas' => ['icon' => 'settings', 'bg' => 'bg-yellow-100 text-yellow-600'],
							'Tindak Lanjut SPPG' => ['icon' => 'home_repair_service', 'bg' => 'bg-blue-100 text-blue-600'],
							'Aduan Diselesaikan oleh Superadmin' => ['icon' => 'flag', 'bg' => 'bg-green-100 text-green-600'],
							'Aduan Diselesaikan oleh Satgas' => ['icon' => 'flag', 'bg' => 'bg-green-100 text-green-600'],
						];

						if (\Illuminate\Support\Str::startsWith($log->description, 'Aduan Direspon oleh Satgas')) {
							$style = $map['Aduan Direspon oleh Satgas'];
						} elseif (\Illuminate\Support\Str::startsWith($log->description, 'Tindak Lanjut SPPG')) {
							$style = $map['Tindak Lanjut SPPG'];
						} else {
							$style = $map[$log->description] ?? ['icon' => 'info', 'bg' => 'bg-gray-100 text-gray-600'];
						}
						$hideLogBody = \Illuminate\Support\Str::startsWith($log->description, 'Aduan Direspon oleh Satgas')
							|| \Illuminate\Support\Str::startsWith($log->description, 'Tindak Lanjut SPPG');
						$logTitle = \Illuminate\Support\Str::startsWith($log->description, 'Tindak Lanjut SPPG')
							&& !\Illuminate\Support\Str::contains($log->description, ':')
								? $log->description.': '.$sppgName
								: $log->description;
					@endphp

					<div class="bg-surface-container-lowest p-5 rounded-2xl flex gap-4 transition-all hover:translate-x-1">

						<div class="h-10 w-10 shrink-0 {{ $style['bg'] }} rounded-full flex items-center justify-center">
							<span class="material-symbols-outlined text-sm">
								{{ $style['icon'] }}
							</span>
						</div>

						<div class="min-w-0">
							<h4 class="font-bold text-sm text-on-surface break-words">
								{{ $logTitle }}
							</h4>

							@if(!$hideLogBody)
								<p class="text-xs text-outline mt-1 leading-relaxed break-words">
									{!! html_entity_decode($log->properties['keterangan'] ?? '-') !!}
								</p>
							@endif

							<span class="text-[10px] text-outline-variant block mt-2">
								{{ $log->created_at->format('d M Y, H:i') }} WIB
							</span>
						</div>

					</div>

					@empty
						<div class="text-sm text-gray-500">
							Belum ada aktivitas
						</div>
					@endforelse

				</div>
			</div>
		</aside>
	</div>
	@endif
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('click', function (event) {
	const link = event.target.closest('[data-tracking-photo]');
	if (!link) {
		return;
	}

	event.preventDefault();
	openTrackingPhotoLightbox(link.href, link.dataset.photoTitle || 'Foto lampiran');
});

function openTrackingPhotoLightbox(src, title) {
	let modal = document.getElementById('tracking-photo-lightbox');
	if (!modal) {
		modal = document.createElement('div');
		modal.id = 'tracking-photo-lightbox';
		modal.className = 'fixed inset-0 z-[9999] hidden items-center justify-center bg-black/75 p-4';
		modal.innerHTML = `
			<div class="relative w-full max-w-4xl overflow-hidden rounded-2xl bg-white shadow-2xl">
				<div class="flex items-center justify-between gap-4 border-b border-surface-container-low px-5 py-4">
					<h3 class="font-bold text-primary" data-lightbox-title>Foto lampiran</h3>
					<button type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-surface-container-low text-primary" data-lightbox-close>
						<span class="material-symbols-outlined">close</span>
					</button>
				</div>
				<div class="bg-surface-container-low p-4">
					<img src="" alt="Foto lampiran" class="max-h-[75vh] w-full rounded-xl object-contain" data-lightbox-image>
				</div>
			</div>
		`;
		document.body.appendChild(modal);
		modal.addEventListener('click', function (event) {
			if (event.target === modal || event.target.closest('[data-lightbox-close]')) {
				modal.classList.add('hidden');
				modal.classList.remove('flex');
			}
		});
	}

	modal.querySelector('[data-lightbox-title]').textContent = title;
	modal.querySelector('[data-lightbox-image]').src = src;
	modal.classList.remove('hidden');
	modal.classList.add('flex');
}
</script>
@endpush
