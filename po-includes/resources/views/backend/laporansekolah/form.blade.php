@php
	$isEdit = isset($laporan) && $laporan;
	$selectedRating = old('rating', $laporan->rating ?? '');
@endphp

<div class="card mg-b-20">
	<div class="card-header"><h6 class="mg-b-0">Data Laporan</h6></div>
	<div class="card-body">
		<div class="form-row">
			<div class="form-group col-md-3">
				{!! Form::label('tanggal', 'Tanggal *', ['class' => 'control-label']) !!}
				{!! Form::date('tanggal', old('tanggal', $isEdit && $laporan->tanggal ? $laporan->tanggal->format('Y-m-d') : date('Y-m-d')), ['class' => $errors->has('tanggal') ? 'form-control is-invalid' : 'form-control', 'required']) !!}
				{!! $errors->first('tanggal', '<p class="text-danger">:message</p>') !!}
			</div>

			@if($isAdminLaporanSekolah)
			<div class="form-group col-md-3">
				{!! Form::label('sekolah_id', 'Sekolah *', ['class' => 'control-label']) !!}
				<select name="sekolah_id" class="form-control {{ $errors->has('sekolah_id') ? 'is-invalid' : '' }}" required>
					<option value="">-- Pilih Sekolah --</option>
					@foreach($sekolahs as $id => $nama)
						<option value="{{ $id }}" {{ old('sekolah_id', $laporan->sekolah_id ?? '') == $id ? 'selected' : '' }}>{{ $nama }}</option>
					@endforeach
				</select>
				{!! $errors->first('sekolah_id', '<p class="text-danger">:message</p>') !!}
			</div>
			@endif

			@if($isSekolahLaporanSekolah)
				<input type="hidden" name="sekolah_id" value="{{ Auth::user()->sekolah_id }}">
			@endif

			<div class="form-group col-md-3">
				{!! Form::label('sppg_id', 'SPPG *', ['class' => 'control-label']) !!}
				<select name="sppg_id" class="form-control {{ $errors->has('sppg_id') ? 'is-invalid' : '' }}" required>
					<option value="">-- Pilih SPPG --</option>
					@foreach($sppgs as $id => $nama)
						<option value="{{ $id }}" {{ old('sppg_id', $laporan->sppg_id ?? '') == $id ? 'selected' : '' }}>{{ $nama }}</option>
					@endforeach
				</select>
				{!! $errors->first('sppg_id', '<p class="text-danger">:message</p>') !!}
			</div>

			<div class="form-group col-md-3">
				{!! Form::label('menu_harian_id', 'Menu Harian', ['class' => 'control-label']) !!}
				<select name="menu_harian_id" class="form-control {{ $errors->has('menu_harian_id') ? 'is-invalid' : '' }}">
					<option value="">-- Tanpa Menu --</option>
					@foreach($menuHarians as $id => $nama)
						<option value="{{ $id }}" {{ old('menu_harian_id', $laporan->menu_harian_id ?? '') == $id ? 'selected' : '' }}>{{ $nama }}</option>
					@endforeach
				</select>
				{!! $errors->first('menu_harian_id', '<p class="text-danger">:message</p>') !!}
			</div>
		</div>
	</div>
</div>

<div class="card mg-b-20">
	<div class="card-header"><h6 class="mg-b-0">Foto Laporan</h6></div>
	<div class="card-body">
		<div class="form-row">
			<div class="form-group col-md-6">
				{!! Form::label('foto_menu', 'Foto Menu'.(!$isEdit || !$laporan->foto_menu ? ' *' : ''), ['class' => 'control-label']) !!}
				{!! Form::file('foto_menu', ['class' => $errors->has('foto_menu') ? 'form-control is-invalid' : 'form-control', 'accept' => 'image/jpeg,image/png']) !!}
				{!! $errors->first('foto_menu', '<p class="text-danger">:message</p>') !!}
				@if($isEdit && $laporan->foto_menu)
					<div class="mg-t-10"><img src="{{ asset('po-content/uploads/'.$laporan->foto_menu) }}" class="img-fluid rounded" style="max-height:180px;" alt=""></div>
				@endif
			</div>
			<div class="form-group col-md-6">
				{!! Form::label('foto_siswa', 'Foto Siswa Makan'.(!$isEdit || !$laporan->foto_siswa ? ' *' : ''), ['class' => 'control-label']) !!}
				{!! Form::file('foto_siswa', ['class' => $errors->has('foto_siswa') ? 'form-control is-invalid' : 'form-control', 'accept' => 'image/jpeg,image/png']) !!}
				{!! $errors->first('foto_siswa', '<p class="text-danger">:message</p>') !!}
				@if($isEdit && $laporan->foto_siswa)
					<div class="mg-t-10"><img src="{{ asset('po-content/uploads/'.$laporan->foto_siswa) }}" class="img-fluid rounded" style="max-height:180px;" alt=""></div>
				@endif
			</div>
		</div>
	</div>
</div>

<div class="card mg-b-20">
	<div class="card-header"><h6 class="mg-b-0">Lokasi dan Rating</h6></div>
	<div class="card-body">
		<div class="form-row">
			<div class="form-group col-md-3">
				{!! Form::label('latitude', 'Latitude', ['class' => 'control-label']) !!}
				{!! Form::text('latitude', old('latitude', $laporan->latitude ?? ''), [
					'class' => $errors->has('latitude') ? 'form-control is-invalid' : 'form-control',
					'id' => 'latitude',
					'readonly',
					'placeholder' => '-6.1200000'
				]) !!}
				{!! $errors->first('latitude', '<p class="text-danger">:message</p>') !!}
			</div>
			<div class="form-group col-md-3">
				{!! Form::label('longitude', 'Longitude', ['class' => 'control-label']) !!}
				{!! Form::text('longitude', old('longitude', $laporan->longitude ?? ''), [
					'class' => $errors->has('longitude') ? 'form-control is-invalid' : 'form-control',
					'id' => 'longitude',
					'readonly',
					'placeholder' => '106.1500000'
				]) !!}
				{!! $errors->first('longitude', '<p class="text-danger">:message</p>') !!}
			</div>
			<div class="form-group col-md-6">
				{!! Form::label('lokasi', 'Lokasi', ['class' => 'control-label']) !!}
				{!! Form::text('lokasi', old('lokasi', $laporan->lokasi ?? ''), ['class' => $errors->has('lokasi') ? 'form-control is-invalid' : 'form-control']) !!}
				{!! $errors->first('lokasi', '<p class="text-danger">:message</p>') !!}
			</div>
			<div class="form-group col-md-12">
				<label class="control-label">Pilih Lokasi Upload dari Peta</label>
				<div class="d-flex align-items-center justify-content-between mg-b-10">
					<small class="text-muted">Klik peta untuk memindahkan marker, atau geser marker ke titik lokasi upload laporan.</small>
					<button type="button" class="btn btn-sm btn-outline-primary" id="btn-use-current-location">
						<i class="fa fa-location-arrow"></i> Lokasi Saya
					</button>
				</div>
				<div id="laporan-location-map" style="height: 380px; border-radius: 8px; overflow: hidden; border: 1px solid #d9dfe7;"></div>
			</div>
			<div class="form-group col-md-12">
				{!! Form::label('rating', 'Rating *', ['class' => 'control-label d-block']) !!}
				<div class="laporan-rating {{ $errors->has('rating') ? 'is-invalid' : '' }}">
					@for($i = 1; $i <= 5; $i++)
						<label class="laporan-rating-option" title="{{ $i }} bintang">
							<input type="radio" name="rating" value="{{ $i }}" {{ (string) $selectedRating === (string) $i ? 'checked' : '' }} required>
							<span class="laporan-rating-star">&#9733;</span>
						</label>
					@endfor
				</div>
				{!! $errors->first('rating', '<p class="text-danger">:message</p>') !!}
			</div>
		</div>
	</div>
</div>

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
	.laporan-rating {
		display: inline-flex;
		flex-direction: row;
		gap: 6px;
		padding: 8px 0;
	}

	.laporan-rating-option {
		margin: 0;
		cursor: pointer;
	}

	.laporan-rating-option input {
		position: absolute;
		opacity: 0;
		pointer-events: none;
	}

	.laporan-rating-star {
		display: inline-flex;
		align-items: center;
		justify-content: center;
		width: 36px;
		height: 36px;
		border: 1px solid #d9dfe7;
		border-radius: 6px;
		color: #b9c2cf;
		font-size: 24px;
		line-height: 1;
		background: #fff;
		transition: color .15s ease, border-color .15s ease, background-color .15s ease;
	}

	.laporan-rating-option:hover .laporan-rating-star,
	.laporan-rating-option input:focus + .laporan-rating-star {
		border-color: #f2b705;
		color: #f2b705;
	}

	.laporan-rating-star.is-active,
	.laporan-rating-option input:checked + .laporan-rating-star {
		border-color: #f2b705;
		background: #fff8df;
		color: #f2b705;
	}

	.laporan-rating.is-invalid .laporan-rating-star {
		border-color: #dc3545;
	}
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
	$(function () {
		function refreshRatingStars() {
			var selected = parseInt($('.laporan-rating input:checked').val() || 0, 10);

			$('.laporan-rating-option').each(function () {
				var optionValue = parseInt($(this).find('input').val(), 10);
				$(this).find('.laporan-rating-star').toggleClass('is-active', optionValue <= selected);
			});
		}

		$('.laporan-rating input').on('change', refreshRatingStars);
		refreshRatingStars();

		var defaultLat = -6.1200000;
		var defaultLng = 106.1500000;
		var initialLat = parseFloat($('#latitude').val()) || defaultLat;
		var initialLng = parseFloat($('#longitude').val()) || defaultLng;

		var map = L.map('laporan-location-map').setView([initialLat, initialLng], ($('#latitude').val() && $('#longitude').val()) ? 16 : 12);
		var marker = L.marker([initialLat, initialLng], { draggable: true }).addTo(map);

		L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
			maxZoom: 19,
			attribution: '&copy; OpenStreetMap'
		}).addTo(map);

		function setCoordinate(lat, lng) {
			$('#latitude').val(Number(lat).toFixed(7));
			$('#longitude').val(Number(lng).toFixed(7));
			marker.setLatLng([lat, lng]);
		}

		if (!$('#latitude').val() || !$('#longitude').val()) {
			$('#latitude').val('');
			$('#longitude').val('');
		}

		map.on('click', function (e) {
			setCoordinate(e.latlng.lat, e.latlng.lng);
		});

		marker.on('dragend', function (e) {
			var position = e.target.getLatLng();
			setCoordinate(position.lat, position.lng);
		});

		$('#btn-use-current-location').on('click', function () {
			if (!navigator.geolocation) {
				alert('Browser tidak mendukung geolocation.');
				return;
			}

			navigator.geolocation.getCurrentPosition(function (position) {
				var lat = position.coords.latitude;
				var lng = position.coords.longitude;
				setCoordinate(lat, lng);
				map.setView([lat, lng], 17);
			}, function () {
				alert('Gagal mengambil lokasi. Pastikan izin lokasi browser aktif.');
			});
		});

		setTimeout(function () {
			map.invalidateSize();
		}, 300);
	});
</script>
@endpush
