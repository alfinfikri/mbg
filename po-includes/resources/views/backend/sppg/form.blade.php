@php
    $selectedSekolahIds = old('sekolah_ids', $selectedSekolahIds ?? []);
@endphp

<div class="card mg-b-20">
    <div class="card-header">
        <h6 class="mg-b-0">Data Profil SPPG</h6>
    </div>
    <div class="card-body">
        <div class="form-row">
            <div class="form-group col-md-12">
                {!! Form::label('nama', 'Nama SPPG *', ['class' => 'control-label']) !!}
                {!! Form::text('nama', old('nama', $sppg->nama ?? ''), [
                    'class' => $errors->has('nama') ? 'form-control is-invalid' : 'form-control',
                    'required'
                ]) !!}
                {!! $errors->first('nama', '<p class="text-danger">:message</p>') !!}
            </div>
        </div>
    </div>
</div>

<div class="card mg-b-20">
    <div class="card-header">
        <h6 class="mg-b-0">Wilayah dan Lokasi</h6>
    </div>
    <div class="card-body">
        <div class="form-row">
            <div class="form-group col-md-3">
                {!! Form::label('kecamatan','Kecamatan', ['class' => 'control-label']) !!}
                <select class="form-control" id="parent_id" name="parent_id">
                    <option value="">-- Pilih Kecamatan --</option>
                    @foreach($kecamatans as $id => $nama)
                        <option value="{{ $id }}"
                            {{ old('parent_id', isset($sppg) && $sppg->wilayah ? optional($sppg->wilayah->parent)->id : '') == $id ? 'selected' : '' }}>
                            {{ $nama }}
                        </option>
                    @endforeach
                </select>
                {!! $errors->first('parent_id', '<p class="text-danger">:message</p>') !!}
            </div>

            <div class="form-group col-md-3">
                {!! Form::label('kelurahan','Kelurahan', ['class' => 'control-label']) !!}
                <select class="form-control" id="wilayah_id" name="wilayah_id">
                    <option value="">-- Pilih Kelurahan --</option>
                </select>
                {!! $errors->first('wilayah_id', '<p class="text-danger">:message</p>') !!}
            </div>

            <div class="form-group col-md-3">
                {!! Form::label('latitude', 'Latitude', ['class' => 'control-label']) !!}
                {!! Form::text('latitude', old('latitude', $sppg->latitude ?? ''), [
                    'class' => $errors->has('latitude') ? 'form-control is-invalid' : 'form-control',
                    'id' => 'latitude',
                    'readonly',
                    'placeholder' => '-6.1200000'
                ]) !!}
                {!! $errors->first('latitude', '<p class="text-danger">:message</p>') !!}
            </div>

            <div class="form-group col-md-3">
                {!! Form::label('longitude', 'Longitude', ['class' => 'control-label']) !!}
                {!! Form::text('longitude', old('longitude', $sppg->longitude ?? ''), [
                    'class' => $errors->has('longitude') ? 'form-control is-invalid' : 'form-control',
                    'id' => 'longitude',
                    'readonly',
                    'placeholder' => '106.1500000'
                ]) !!}
                {!! $errors->first('longitude', '<p class="text-danger">:message</p>') !!}
            </div>

            <div class="form-group col-md-12">
                <label class="control-label">Pilih Lokasi SPPG dari Peta</label>
                <div class="d-flex align-items-center justify-content-between mg-b-10">
                    <small class="text-muted">Klik peta untuk memindahkan marker, atau geser marker ke titik lokasi SPPG.</small>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="btn-use-current-location">
                        <i class="fa fa-location-arrow"></i> Lokasi Saya
                    </button>
                </div>
                <div id="sppg-location-map" style="height: 380px; border-radius: 8px; overflow: hidden; border: 1px solid #d9dfe7;"></div>
            </div>

            <div class="form-group col-md-12">
                {!! Form::label('alamat', 'Alamat', ['class' => 'control-label']) !!}
                {!! Form::textarea('alamat', old('alamat', $sppg->alamat ?? ''), [
                    'class' => $errors->has('alamat') ? 'form-control is-invalid' : 'form-control',
                    'rows' => 3
                ]) !!}
                {!! $errors->first('alamat', '<p class="text-danger">:message</p>') !!}
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
    #sppg-location-map,
    #sppg-location-map .leaflet-pane,
    #sppg-location-map .leaflet-top,
    #sppg-location-map .leaflet-bottom {
        z-index: 1 !important;
    }

    .select2-container--open,
    .select2-dropdown {
        z-index: 3000 !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    $(function () {
        var defaultLat = -6.1200000;
        var defaultLng = 106.1500000;
        var initialLat = parseFloat($('#latitude').val()) || defaultLat;
        var initialLng = parseFloat($('#longitude').val()) || defaultLng;

        var map = L.map('sppg-location-map').setView([initialLat, initialLng], ($('#latitude').val() && $('#longitude').val()) ? 16 : 12);
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

<div class="card mg-b-20">
    <div class="card-header">
        <h6 class="mg-b-0">Kontak dan Penanggung Jawab</h6>
    </div>
    <div class="card-body">
        <div class="form-row">
            <div class="form-group col-md-4">
                {!! Form::label('nama_penanggung_jawab', 'Nama Penanggung Jawab', ['class' => 'control-label']) !!}
                {!! Form::text('nama_penanggung_jawab', old('nama_penanggung_jawab', $sppg->nama_penanggung_jawab ?? ''), [
                    'class' => $errors->has('nama_penanggung_jawab') ? 'form-control is-invalid' : 'form-control'
                ]) !!}
                {!! $errors->first('nama_penanggung_jawab', '<p class="text-danger">:message</p>') !!}
            </div>

            <div class="form-group col-md-4">
                {!! Form::label('no_hp', 'Nomor HP', ['class' => 'control-label']) !!}
                {!! Form::text('no_hp', old('no_hp', $sppg->no_hp ?? ''), [
                    'class' => $errors->has('no_hp') ? 'form-control is-invalid' : 'form-control'
                ]) !!}
                {!! $errors->first('no_hp', '<p class="text-danger">:message</p>') !!}
            </div>

            <div class="form-group col-md-4">
                {!! Form::label('email', 'Email', ['class' => 'control-label']) !!}
                {!! Form::email('email', old('email', $sppg->email ?? ''), [
                    'class' => $errors->has('email') ? 'form-control is-invalid' : 'form-control'
                ]) !!}
                {!! $errors->first('email', '<p class="text-danger">:message</p>') !!}
            </div>
        </div>
    </div>
</div>

<div class="card mg-b-20">
    <div class="card-header">
        <h6 class="mg-b-0">Sertifikat Laik Higiene Sanitasi dan Halal</h6>
    </div>
    <div class="card-body">
        <div class="form-row">
            <div class="form-group col-md-3">
                {!! Form::label('slhs_nomor', 'Nomor SLHS', ['class' => 'control-label']) !!}
                {!! Form::text('slhs_nomor', old('slhs_nomor', $sppg->slhs_nomor ?? ''), [
                    'class' => $errors->has('slhs_nomor') ? 'form-control is-invalid' : 'form-control'
                ]) !!}
                {!! $errors->first('slhs_nomor', '<p class="text-danger">:message</p>') !!}
            </div>

            <div class="form-group col-md-3">
                {!! Form::label('slhs_tanggal_terbit', 'Tanggal Terbit SLHS', ['class' => 'control-label']) !!}
                {!! Form::date('slhs_tanggal_terbit', old('slhs_tanggal_terbit', $sppg->slhs_tanggal_terbit ?? $sppg->slhs_tanggal ?? ''), [
                    'class' => $errors->has('slhs_tanggal_terbit') || $errors->has('slhs_tanggal') ? 'form-control is-invalid' : 'form-control'
                ]) !!}
                {!! $errors->first('slhs_tanggal_terbit', '<p class="text-danger">:message</p>') !!}
                {!! $errors->first('slhs_tanggal', '<p class="text-danger">:message</p>') !!}
            </div>

            <div class="form-group col-md-3">
                {!! Form::label('slhs_berlaku_hingga', 'SLHS Berlaku Hingga', ['class' => 'control-label']) !!}
                {!! Form::date('slhs_berlaku_hingga', old('slhs_berlaku_hingga', $sppg->slhs_berlaku_hingga ?? ''), [
                    'class' => $errors->has('slhs_berlaku_hingga') ? 'form-control is-invalid' : 'form-control'
                ]) !!}
                {!! $errors->first('slhs_berlaku_hingga', '<p class="text-danger">:message</p>') !!}
            </div>

            <div class="form-group col-md-3">
                {!! Form::label('slhs_file','File SLHS', ['class' => 'control-label']) !!}
                <div class="input-group">
                    {!! Form::text('slhs_file', old('slhs_file', $sppg->slhs_file ?? ''), ['class' => $errors->has('slhs_file') ? 'form-control is-invalid' : 'form-control', 'id' => 'input-filemanager-slhs-file']) !!}
                    <span class="input-group-append">
                        <a href="{{ url('po-content/cms-pemkot/file/dialog.php?type=1&field_id=input-filemanager-slhs-file&relative_url=1&akey='.config('fm.key')) }}" id="filemanager" class="btn btn-secondary"><i class="fa fa-file"></i> {{ __('general.browse') }}</a>
                    </span>
                </div>
                {!! $errors->first('slhs_file', '<p class="text-danger">:message</p>') !!}
            </div>

            <div class="form-group col-md-4">
                {!! Form::label('halal_nomor', 'Nomor Sertifikat Halal', ['class' => 'control-label']) !!}
                {!! Form::text('halal_nomor', old('halal_nomor', $sppg->halal_nomor ?? ''), [
                    'class' => $errors->has('halal_nomor') ? 'form-control is-invalid' : 'form-control'
                ]) !!}
                {!! $errors->first('halal_nomor', '<p class="text-danger">:message</p>') !!}
            </div>

            <div class="form-group col-md-4">
                {!! Form::label('halal_tanggal_terbit', 'Tanggal Terbit Sertifikat Halal', ['class' => 'control-label']) !!}
                {!! Form::date('halal_tanggal_terbit', old('halal_tanggal_terbit', $sppg->halal_tanggal_terbit ?? ''), [
                    'class' => $errors->has('halal_tanggal_terbit') ? 'form-control is-invalid' : 'form-control'
                ]) !!}
                {!! $errors->first('halal_tanggal_terbit', '<p class="text-danger">:message</p>') !!}
            </div>

            <div class="form-group col-md-4">
                {!! Form::label('halal_file','File Sertifikat Halal', ['class' => 'control-label']) !!}
                <div class="input-group">
                    {!! Form::text('halal_file', old('halal_file', $sppg->halal_file ?? ''), ['class' => $errors->has('halal_file') ? 'form-control is-invalid' : 'form-control', 'id' => 'input-filemanager-halal-file']) !!}
                    <span class="input-group-append">
                        <a href="{{ url('po-content/cms-pemkot/file/dialog.php?type=1&field_id=input-filemanager-halal-file&relative_url=1&akey='.config('fm.key')) }}" class="btn btn-secondary filemanager-btn"><i class="fa fa-file"></i> {{ __('general.browse') }}</a>
                    </span>
                </div>
                {!! $errors->first('halal_file', '<p class="text-danger">:message</p>') !!}
            </div>
        </div>
    </div>
</div>

<div class="card mg-b-20">
    <div class="card-header">
        <h6 class="mg-b-0">Dapur dan Ahli Gizi</h6>
    </div>
    <div class="card-body">
        <div class="form-row">
            <div class="form-group col-md-6">
                {!! Form::label('foto_dapur','Foto Dapur', ['class' => 'control-label']) !!}
                <div class="input-group">
                    {!! Form::text('foto_dapur', old('foto_dapur', $sppg->foto_dapur ?? ''), ['class' => $errors->has('foto_dapur') ? 'form-control is-invalid' : 'form-control', 'id' => 'input-filemanager-foto-dapur']) !!}
                    <span class="input-group-append">
                        <a href="{{ url('po-content/cms-pemkot/file/dialog.php?type=1&field_id=input-filemanager-foto-dapur&relative_url=1&akey='.config('fm.key')) }}" id="filemanager-multi" class="btn btn-secondary"><i class="fa fa-file"></i> {{ __('general.browse') }}</a>
                    </span>
                </div>
                {!! $errors->first('foto_dapur', '<p class="text-danger">:message</p>') !!}
            </div>

            <div class="form-group col-md-6">
                {!! Form::label('nama_ahli_gizi', 'Nama Ahli Gizi', ['class' => 'control-label']) !!}
                {!! Form::text('nama_ahli_gizi', old('nama_ahli_gizi', $sppg->nama_ahli_gizi ?? ''), [
                    'class' => $errors->has('nama_ahli_gizi') ? 'form-control is-invalid' : 'form-control'
                ]) !!}
                {!! $errors->first('nama_ahli_gizi', '<p class="text-danger">:message</p>') !!}
            </div>

            <div class="form-group col-md-12">
                {!! Form::label('keterangan_data_profil', 'Keterangan Data Profil Ahli Gizi', ['class' => 'control-label']) !!}
                {!! Form::textarea('keterangan_data_profil', old('keterangan_data_profil', $sppg->keterangan_data_profil ?? ''), [
                    'class' => $errors->has('keterangan_data_profil') ? 'form-control is-invalid' : 'form-control',
                    'rows' => 3
                ]) !!}
                {!! $errors->first('keterangan_data_profil', '<p class="text-danger">:message</p>') !!}
            </div>

            <div class="form-group col-md-12">
                {!! Form::label('fasilitas_dapur', 'Fasilitas Dapur', ['class' => 'control-label']) !!}
                {!! Form::textarea('fasilitas_dapur', old('fasilitas_dapur', $sppg->fasilitas_dapur ?? ''), [
                    'class' => $errors->has('fasilitas_dapur') ? 'form-control is-invalid' : 'form-control',
                    'rows' => 3
                ]) !!}
                {!! $errors->first('fasilitas_dapur', '<p class="text-danger">:message</p>') !!}
            </div>
        </div>
    </div>
</div>

<div class="card mg-b-20">
    <div class="card-header">
        <h6 class="mg-b-0">Kapasitas dan Status Layanan</h6>
    </div>
    <div class="card-body">
        <div class="form-row">
            <div class="form-group col-md-4">
                {!! Form::label('kapasitas_produksi', 'Kapasitas Produksi', ['class' => 'control-label']) !!}
                {!! Form::number('kapasitas_produksi', old('kapasitas_produksi', $sppg->kapasitas_produksi ?? 0), [
                    'class' => $errors->has('kapasitas_produksi') ? 'form-control is-invalid' : 'form-control',
                    'min' => 0
                ]) !!}
                {!! $errors->first('kapasitas_produksi', '<p class="text-danger">:message</p>') !!}
            </div>

            <div class="form-group col-md-4">
                {!! Form::label('jumlah_petugas', 'Jumlah Petugas', ['class' => 'control-label']) !!}
                {!! Form::number('jumlah_petugas', old('jumlah_petugas', $sppg->jumlah_petugas ?? 0), [
                    'class' => $errors->has('jumlah_petugas') ? 'form-control is-invalid' : 'form-control',
                    'min' => 0
                ]) !!}
                {!! $errors->first('jumlah_petugas', '<p class="text-danger">:message</p>') !!}
            </div>

            <div class="form-group col-md-4">
                {!! Form::label('status_layanan','Status Layanan *', ['class' => 'control-label']) !!}
                <select class="form-control" id="status_layanan" name="status_layanan" required>
                    <option value="1" {{ old('status_layanan', $sppg->status_layanan ?? 1) == '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="2" {{ old('status_layanan', $sppg->status_layanan ?? 1) == '2' ? 'selected' : '' }}>Tidak Aktif</option>
                    <option value="3" {{ old('status_layanan', $sppg->status_layanan ?? 1) == '3' ? 'selected' : '' }}>Belum Operasi</option>
                </select>
                {!! $errors->first('status_layanan', '<p class="text-danger">:message</p>') !!}
            </div>
        </div>
    </div>
</div>

<div class="card mg-b-20">
    <div class="card-header">
        <h6 class="mg-b-0">Sekolah yang Dilayani</h6>
    </div>
    <div class="card-body">
        <div class="form-row">
            <div class="form-group col-md-12">
                {!! Form::label('sekolah_ids', 'Sekolah yang Dilayani', ['class' => 'control-label']) !!}
                <select name="sekolah_ids[]" id="sekolah_ids"
                    class="form-control {{ $errors->has('sekolah_ids') ? 'is-invalid' : '' }}"
                    multiple>
                    @foreach($sekolahs as $id => $nama)
                        <option value="{{ $id }}" {{ in_array($id, $selectedSekolahIds) ? 'selected' : '' }}>
                            {{ $nama }}
                        </option>
                    @endforeach
                </select>
                {!! $errors->first('sekolah_ids', '<p class="text-danger">:message</p>') !!}
            </div>
        </div>
    </div>
</div>
