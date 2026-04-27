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
        {!! Form::label('jenis_id','Jenjang Sekolah *', ['class' => 'control-label']) !!}
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

    {{-- Jumlah --}}
    <div class="form-group col-md-6">
        {!! Form::label('jumlah_total', 'Jumlah Siswa *', ['class' => 'control-label']) !!}
        {!! Form::number('jumlah_total', old('jumlah_total', $sekolah->jumlah_total ?? null), [
            'class' => $errors->has('jumlah_total') ? 'form-control is-invalid' : 'form-control',
            'required'
        ]) !!}
        {!! $errors->first('jumlah_total', '<p class="text-danger">:message</p>') !!}
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

</div>