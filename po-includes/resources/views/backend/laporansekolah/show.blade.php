@extends('layouts.app')
@section('title', 'Detail Laporan Sekolah')

@php
	$status = [
		1 => ['Sudah Lapor', 'badge-info'],
		2 => ['Terverifikasi', 'badge-success'],
		3 => ['Perlu Perbaikan / Ditolak', 'badge-danger'],
	][$laporan->status_laporan] ?? ['Belum Lapor', 'badge-light'];
	$hasCoordinate = !empty($laporan->latitude) && !empty($laporan->longitude);
	$rating = (int) $laporan->rating;
@endphp

@push('styles')
@if($hasCoordinate)
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
	#laporan-location-map {
		height: 180px;
		width: 100%;
		border-radius: 8px;
	}
</style>
@endif
@endpush

@section('content')
	<div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-20">
		<div>
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb breadcrumb-style1 mg-b-10">
					<li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ __('general.dashboard') }}</a></li>
					<li class="breadcrumb-item"><a href="{{ url('/dashboard/laporan-sekolahs/table') }}">Laporan Sekolah</a></li>
					<li class="breadcrumb-item active" aria-current="page">Detail</li>
				</ol>
			</nav>
			<h4 class="mg-b-0 tx-spacing--1">Laporan {{ optional($laporan->tanggal)->translatedFormat('l, d F Y') ?: '-' }}</h4>
		</div>
		<div>
			<a href="{{ url('dashboard/laporan-sekolahs/table') }}" class="btn btn-sm pd-x-15 btn-white btn-uppercase mg-t-10"><i data-feather="arrow-left" class="wd-10 mg-r-5"></i> {{ __('general.back') }}</a>
			@if(Auth::user()->can('update-laporan-sekolahs') && ((Auth::user()->sekolah_id && Auth::user()->sekolah_id == $laporan->sekolah_id) || Auth::user()->hasRole('superadmin') || Auth::user()->hasRole('superadmin 2') || Auth::user()->hasRole('admin')))
				<a href="{{ url('dashboard/laporan-sekolahs/'.Hashids::encode($laporan->id).'/edit') }}" class="btn btn-sm pd-x-15 btn-primary btn-uppercase mg-t-10"><i data-feather="edit" class="wd-10 mg-r-5"></i> {{ __('general.edit') }}</a>
			@endif
		</div>
	</div>

	<div class="row row-xs mg-b-20">
		<div class="col-md-4">
			<div class="card"><div class="card-body">
				<span class="tx-12 tx-color-03">Waktu Laporan</span>
				<h5 class="mg-b-5">{{ optional($laporan->tanggal)->format('d-m-Y') ?: '-' }}</h5>
				<p class="mg-b-0 tx-color-03">
					Rating:
					@if($rating)
						<span class="tx-warning">
							@for($i = 1; $i <= 5; $i++)
								{!! $i <= $rating ? '&#9733;' : '&#9734;' !!}
							@endfor
						</span>
						<span>({{ $rating }}/5)</span>
					@else
						-
					@endif
				</p>
			</div></div>
		</div>
		<div class="col-md-4">
			<div class="card"><div class="card-body">
				<span class="tx-12 tx-color-03">Lokasi Upload</span>
				<h5 class="mg-b-5">{{ $laporan->lokasi ?: '-' }}</h5>
				<p class="mg-b-0 tx-color-03">{{ $laporan->latitude && $laporan->longitude ? $laporan->latitude.', '.$laporan->longitude : '-' }}</p>
				@if($hasCoordinate)
					<div id="laporan-location-map" class="mg-t-15"></div>
				@else
					<div class="bg-light rounded d-flex align-items-center justify-content-center mg-t-15" style="height:180px;">
						<span class="tx-color-03">Koordinat belum tersedia</span>
					</div>
				@endif
			</div></div>
		</div>
		<div class="col-md-4">
			<div class="card"><div class="card-body">
				<span class="tx-12 tx-color-03">Status</span><br>
				<span class="badge {{ $status[1] }} mg-t-10">{{ $status[0] }}</span>
			</div></div>
		</div>
	</div>

	<div class="card mg-b-20">
		<div class="card-header"><h6 class="mg-b-0">Foto Laporan</h6></div>
		<div class="card-body">
			<div class="row">
				<div class="col-md-6">
					<h6>Foto Menu</h6>
					@if($laporan->foto_menu)
						<a href="{{ asset('po-content/uploads/'.$laporan->foto_menu) }}" target="_blank"><img src="{{ asset('po-content/uploads/'.$laporan->foto_menu) }}" class="img-fluid rounded" style="max-height:360px;" alt=""></a>
					@else
						<p class="tx-color-03">-</p>
					@endif
				</div>
				<div class="col-md-6">
					<h6>Foto Siswa Makan</h6>
					@if($laporan->foto_siswa)
						<a href="{{ asset('po-content/uploads/'.$laporan->foto_siswa) }}" target="_blank"><img src="{{ asset('po-content/uploads/'.$laporan->foto_siswa) }}" class="img-fluid rounded" style="max-height:360px;" alt=""></a>
					@else
						<p class="tx-color-03">-</p>
					@endif
				</div>
			</div>
		</div>
	</div>

	<div class="card">
		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-striped">
					<tbody>
						<tr><th style="width:220px;">Nama Sekolah</th><td>{{ optional($laporan->sekolah)->nama ?? '-' }}</td></tr>
						<tr><th>Nama SPPG</th><td>{{ optional($laporan->sppg)->nama ?? '-' }}</td></tr>
						<tr><th>Menu Harian</th><td>{{ optional($laporan->menuHarian)->nama ?? '-' }}</td></tr>
						<tr>
							<th>Rating</th>
							<td>
								@if($rating)
									<span class="tx-warning">
										@for($i = 1; $i <= 5; $i++)
											{!! $i <= $rating ? '&#9733;' : '&#9734;' !!}
										@endfor
									</span>
									<span class="tx-color-03 mg-l-5">({{ $rating }}/5)</span>
								@else
									-
								@endif
							</td>
						</tr>
						<tr><th>Catatan Verifikasi</th><td>{{ $laporan->catatan_verifikasi ?: '-' }}</td></tr>
						<tr><th>Verified By</th><td>{{ optional($laporan->verifiedBy)->name ?? '-' }}</td></tr>
						<tr><th>Verified At</th><td>{{ optional($laporan->verified_at)->format('d-m-Y H:i') ?: '-' }}</td></tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
@endsection

@push('scripts')
@if($hasCoordinate)
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
	document.addEventListener('DOMContentLoaded', function () {
		const lat = {{ (float) $laporan->latitude }};
		const lng = {{ (float) $laporan->longitude }};
		const popupTitle = @json(optional($laporan->sekolah)->nama ?? 'Lokasi Laporan');
		const popupLocation = @json($laporan->lokasi ?: 'Lokasi upload');
		const map = L.map('laporan-location-map').setView([lat, lng], 16);

		L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
			maxZoom: 19,
			attribution: '&copy; OpenStreetMap'
		}).addTo(map);

		L.marker([lat, lng])
			.addTo(map)
			.bindPopup(`<strong>${popupTitle}</strong><br>${popupLocation}`);

		setTimeout(function () {
			map.invalidateSize();
		}, 150);
	});
</script>
@endif
@endpush
