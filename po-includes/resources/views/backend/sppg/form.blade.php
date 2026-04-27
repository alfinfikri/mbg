<div class="form-row">

    {{-- Nama --}}
    <div class="form-group col-md-12">
        {!! Form::label('nama', 'Nama SPPG *', ['class' => 'control-label']) !!}
        {!! Form::text('nama', old('nama', $sppg->nama ?? ''), [
            'class' => $errors->has('nama') ? 'form-control is-invalid' : 'form-control',
            'required'
        ]) !!}
        {!! $errors->first('nama', '<p class="text-danger">:message</p>') !!}
    </div>

    {{-- Kecamatan --}}
    <div class="form-group col-md-3">
        {!! Form::label('kecamatan','Kecamatan *', ['class' => 'control-label']) !!}
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

    {{-- Kelurahan --}}
    <div class="form-group col-md-3">
        {!! Form::label('kelurahan','Kelurahan *', ['class' => 'control-label']) !!}
        <select class="form-control" id="wilayah_id" name="wilayah_id">
            <option value="">-- Pilih Kelurahan --</option>
        </select>
        {!! $errors->first('wilayah_id', '<p class="text-danger">:message</p>') !!}
    </div>

    {{-- Alamat --}}
    <div class="form-group col-md-6">
        {!! Form::label('alamat', 'Alamat SPPG *', ['class' => 'control-label']) !!}
        
        {!! Form::textarea('alamat', old('alamat', $sppg->alamat ?? ''), [
            'class' => $errors->has('alamat') ? 'form-control is-invalid' : 'form-control',
            'rows' => 3
        ]) !!}
        
        {!! $errors->first('alamat', '<p class="text-danger">:message</p>') !!}
    </div>

    {{-- Sekolah --}}
    <div class="form-group col-md-12">
        {!! Form::label('sekolah_id', 'Sekolah Penerima MBG *', ['class' => 'control-label']) !!}
        
        <select name="sekolah_id[]" id="sekolah_id"
            class="form-control {{ $errors->has('sekolah_id') ? 'is-invalid' : '' }}"
            multiple>
            
            {{-- untuk edit --}}
            @if(isset($sppg) && $sppg->sekolahs)
                @foreach($sppg->sekolahs as $s)
                    <option value="{{ $s->id }}" selected>{{ $s->nama }}</option>
                @endforeach
            @endif

        </select>

        {!! $errors->first('sekolah_id', '<p class="text-danger">:message</p>') !!}
    </div>

</div>