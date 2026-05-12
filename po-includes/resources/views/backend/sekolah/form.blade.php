@php
    $penerimas = $penerimas ?? collect();
    $jumlahBumil = old('jumlah_bumil', $penerimas['bumil'] ?? null);
    $jumlahBusui = old('jumlah_busui', $penerimas['busui'] ?? null);
    $jumlahBalita = old('jumlah_balita', $penerimas['balita'] ?? null);
    $jumlahSiswa = old('jumlah_total', $penerimas['siswa'] ?? ($sekolah->jumlah_total ?? null));
@endphp

<div class="form-row">

    {{-- Nama --}}
    <div class="form-group col-md-12">
        {!! Form::label('nama', 'Nama Sekolah *', ['class' => 'control-label']) !!}
        {!! Form::text('nama', old('nama', $sekolah->nama ?? null), [
            'class' => $errors->has('nama') ? 'form-control is-invalid' : 'form-control',
            'required'
        ]) !!}
        {!! $errors->first('nama', '<p class="text-danger">:message</p>') !!}
    </div>

    {{-- Jenis --}}
    <div class="form-group col-md-3">
        {!! Form::label('jenis_id','Jenis Sekolah *', ['class' => 'control-label']) !!}
        <select class="form-control" id="jenis_id" name="jenis_id">
            <option value="1" {{ old('jenis_id', $sekolah->jenis_id ?? '') == 1 ? 'selected' : '' }}>Posyandu</option>
            <option value="2" {{ old('jenis_id', $sekolah->jenis_id ?? '') == 2 ? 'selected' : '' }}>KB</option>
            <option value="3" {{ old('jenis_id', $sekolah->jenis_id ?? '') == 3 ? 'selected' : '' }}>TK/PAUD</option>
            <option value="4" {{ old('jenis_id', $sekolah->jenis_id ?? '') == 4 ? 'selected' : '' }}>Sekolah Dasar </option>
            <option value="5" {{ old('jenis_id', $sekolah->jenis_id ?? '') == 5 ? 'selected' : '' }}>Sekolah Menengah Pertama</option>
            <option value="6" {{ old('jenis_id', $sekolah->jenis_id ?? '') == 6 ? 'selected' : '' }}>Sekolah Menengah Atas</option>
        </select>
        {!! $errors->first('jenis_id', '<p class="text-danger">:message</p>') !!}
    </div>

    {{-- Status --}}
    <div class="form-group col-md-3">
        {!! Form::label('status_layanan','Status Layanan *', ['class' => 'control-label']) !!}
        <select class="form-control" id="status_layanan" name="status_layanan">
            <option value="1" {{ old('status_layanan', $sekolah->status_layanan ?? '') == '1' ? 'selected' : '' }}>Aktif</option>
            <option value="2" {{ old('status_layanan', $sekolah->status_layanan ?? '') == '2' ? 'selected' : '' }}>Tidak Aktif</option>
            <option value="3" {{ old('status_layanan', $sekolah->status_layanan ?? '') == '3' ? 'selected' : '' }}>Menolak</option>
        </select>
        {!! $errors->first('status_layanan', '<p class="text-danger">:message</p>') !!}
    </div>

    {{-- Kecamatan --}}
    <div class="form-group col-md-3">
        {!! Form::label('kecamatan','Kecamatan *', ['class' => 'control-label']) !!}
        <select class="form-control" id="parent_id" name="parent_id">
            <option value="">-- Pilih Kecamatan --</option>
            @foreach($kecamatans as $id => $nama)
                <option value="{{ $id }}"
                    {{ old('parent_id', isset($sekolah) && $sekolah->wilayah ? optional($sekolah->wilayah->parent)->id : '') == $id ? 'selected' : '' }}>
                    {{ $nama }}
                </option>
            @endforeach
        </select>
        {!! $errors->first('parent_id', '<p class="text-danger">:message</p>') !!}
    </div>

    {{-- Kelurahan --}}
    <div class="form-group col-md-3">
        {!! Form::label('kelurahan','Kelurahan *', ['class' => 'control-label']) !!}
        <select class="form-control" id="wilayah_id" name="wilayah_id">
            <option value="">-- Pilih Kelurahan --</option>
        </select>
        {!! $errors->first('wilayah_id', '<p class="text-danger">:message</p>') !!}
    </div>

    {{-- Jumlah Siswa --}}
    <div class="form-group col-md-6 jumlah-siswa-wrapper">
        {!! Form::label('jumlah_total', 'Jumlah Siswa *', ['class' => 'control-label']) !!}
        {!! Form::number('jumlah_total', $jumlahSiswa, [
            'class' => $errors->has('jumlah_total') ? 'form-control is-invalid' : 'form-control',
            'min' => 0
        ]) !!}
        {!! $errors->first('jumlah_total', '<p class="text-danger">:message</p>') !!}
    </div>

    {{-- Jumlah Posyandu/KB --}}
    <div class="form-group col-md-2 jumlah-ibu-balita-wrapper">
        {!! Form::label('jumlah_bumil', 'Jumlah Bumil *', ['class' => 'control-label']) !!}
        {!! Form::number('jumlah_bumil', $jumlahBumil, [
            'class' => $errors->has('jumlah_bumil') ? 'form-control is-invalid' : 'form-control',
            'min' => 0
        ]) !!}
        {!! $errors->first('jumlah_bumil', '<p class="text-danger">:message</p>') !!}
    </div>

    <div class="form-group col-md-2 jumlah-ibu-balita-wrapper">
        {!! Form::label('jumlah_busui', 'Jumlah Busui *', ['class' => 'control-label']) !!}
        {!! Form::number('jumlah_busui', $jumlahBusui, [
            'class' => $errors->has('jumlah_busui') ? 'form-control is-invalid' : 'form-control',
            'min' => 0
        ]) !!}
        {!! $errors->first('jumlah_busui', '<p class="text-danger">:message</p>') !!}
    </div>

    <div class="form-group col-md-2 jumlah-ibu-balita-wrapper">
        {!! Form::label('jumlah_balita', 'Jumlah Balita *', ['class' => 'control-label']) !!}
        {!! Form::number('jumlah_balita', $jumlahBalita, [
            'class' => $errors->has('jumlah_balita') ? 'form-control is-invalid' : 'form-control',
            'min' => 0
        ]) !!}
        {!! $errors->first('jumlah_balita', '<p class="text-danger">:message</p>') !!}
    </div>

    {{-- Alamat --}}
	<div class="form-group col-md-6">
		{!! Form::label('alamat', 'Alamat Sekolah', ['class' => 'control-label']) !!}
		
		{!! Form::textarea('alamat', old('alamat', $sekolah->alamat ?? null), [
			'class' => $errors->has('alamat') ? 'form-control is-invalid' : 'form-control',
			'rows' => 3
		]) !!}
		
		{!! $errors->first('alamat', '<p class="text-danger">:message</p>') !!}
	</div>

    <div class="form-group col-md-3">
        {!! Form::label('latitude', 'Latitude', ['class' => 'control-label']) !!}
        {!! Form::text('latitude', old('latitude', $sekolah->latitude ?? null), [
            'class' => $errors->has('latitude') ? 'form-control is-invalid' : 'form-control',
            'placeholder' => '-6.1200000',
            'id' => 'latitude',
            'readonly'
        ]) !!}
        {!! $errors->first('latitude', '<p class="text-danger">:message</p>') !!}
    </div>

    <div class="form-group col-md-3">
        {!! Form::label('longitude', 'Longitude', ['class' => 'control-label']) !!}
        {!! Form::text('longitude', old('longitude', $sekolah->longitude ?? null), [
            'class' => $errors->has('longitude') ? 'form-control is-invalid' : 'form-control',
            'placeholder' => '106.1500000',
            'id' => 'longitude',
            'readonly'
        ]) !!}
        {!! $errors->first('longitude', '<p class="text-danger">:message</p>') !!}
    </div>

    <div class="form-group col-md-12">
        <label class="control-label">Pilih Lokasi Sekolah dari Peta</label>
        <div class="d-flex align-items-center justify-content-between mg-b-10">
            <small class="text-muted">Klik peta untuk memindahkan marker, atau geser marker ke titik lokasi sekolah.</small>
            <button type="button" class="btn btn-sm btn-outline-primary" id="btn-use-current-location">
                <i class="fa fa-location-arrow"></i> Lokasi Saya
            </button>
        </div>
        <div id="sekolah-location-map" style="height: 380px; border-radius: 8px; overflow: hidden; border: 1px solid #d9dfe7;"></div>
    </div>

</div>

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script type="text/javascript">
    $(function () {
        var defaultLat = -6.1200000;
        var defaultLng = 106.1500000;
        var initialLat = parseFloat($('#latitude').val()) || defaultLat;
        var initialLng = parseFloat($('#longitude').val()) || defaultLng;

        var map = L.map('sekolah-location-map').setView([initialLat, initialLng], ($('#latitude').val() && $('#longitude').val()) ? 16 : 12);
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

        function toggleJumlahPenerima() {
            var jenisId = parseInt($('#jenis_id').val(), 10);
            var isIbuBalita = jenisId === 1 || jenisId === 2;

            $('.jumlah-ibu-balita-wrapper').toggle(isIbuBalita);
            $('.jumlah-siswa-wrapper').toggle(!isIbuBalita);

            $('#jumlah_bumil, #jumlah_busui, #jumlah_balita').prop('required', isIbuBalita);
            $('#jumlah_total').prop('required', !isIbuBalita);
        }

        toggleJumlahPenerima();
        $('#jenis_id').on('change', toggleJumlahPenerima);
    });
</script>
@endpush
