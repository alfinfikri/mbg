<div class="form-row">

    {{-- SPPG --}}
    @if(!Auth::user()->hasRole('sppg'))
    <div class="form-group col-md-12">
        {!! Form::label('sppg_id', 'SPPG *', ['class' => 'control-label']) !!}
        
        <select name="sppg_id" id="sppg_id"
            class="form-control {{ $errors->has('sppg_id') ? 'is-invalid' : '' }}">
            
            <option value="">-- Pilih SPPG --</option>

            @foreach($sppgs as $id => $nama)
                <option value="{{ $id }}"
                    {{ old('sppg_id', $menuharian->sppg_id ?? '') == $id ? 'selected' : '' }}>
                    {{ $nama }}
                </option>
            @endforeach

        </select>

        {!! $errors->first('sppg_id', '<p class="text-danger">:message</p>') !!}
    </div>
    @endif
    @if(Auth::user()->hasRole('sppg'))
        <input type="hidden" name="sppg_id" value="{{ Auth::user()->sppg_id }}">
    @endif

    {{-- Tanggal --}}
    <div class="form-group col-md-3">
        {!! Form::label('tanggal', 'Tanggal *', ['class' => 'control-label']) !!}
        {!! Form::date('tanggal', old('tanggal', $menuharian->tanggal ?? ''), [
            'class' => $errors->has('tanggal') ? 'form-control is-invalid' : 'form-control',
            'required'
        ]) !!}
        {!! $errors->first('tanggal', '<p class="text-danger">:message</p>') !!}
    </div>

    {{-- Nama --}}
    <div class="form-group col-md-9">
        {!! Form::label('nama', 'Nama Menu *', ['class' => 'control-label']) !!}
        {!! Form::text('nama', old('nama', $menuharian->nama ?? ''), [
            'class' => $errors->has('nama') ? 'form-control is-invalid' : 'form-control',
            'required'
        ]) !!}
        {!! $errors->first('nama', '<p class="text-danger">:message</p>') !!}
    </div>


    {{-- Item Menu --}}
    <div class="form-group col-md-6">
        {!! Form::label('deskripsi', 'Item Menu', ['class' => 'control-label']) !!}

        {!! Form::textarea('deskripsi', old('deskripsi', $menuharian->deskripsi ?? ''), [
            'class' => $errors->has('deskripsi') ? 'form-control is-invalid' : 'form-control',
            'rows' => 3,
            'placeholder' => 'Contoh: Nasi, Telur, Sayur, Jeruk'
        ]) !!}
        
        {!! $errors->first('deskripsi', '<p class="text-danger">:message</p>') !!}
        <small class="text-muted d-block mb-2">
            Masukkan daftar menu, pisahkan dengan tanda koma. 
            Contoh: Nasi, Telur Saos Mentega, Tumis Sayur, Buah Jeruk
        </small>
    </div>

    {{-- Foto --}}
    <div class="form-group col-md-6">
        {!! Form::label('foto','Foto Menu *(.jpg)', ['class' => 'control-label']) !!}
        <div class="input-group">
            {!! Form::text('foto', null, ['class' => $errors->has('foto') ? 'form-control is-invalid' : 'form-control', 'id' => 'input-filemanager']) !!}
            <span class="input-group-append">
                <a href="{{ url('po-content/cms-pemkot/file/dialog.php?type=1&field_id=input-filemanager&relative_url=1&akey='.config('fm.key')) }}" id="filemanager" class="btn btn-secondary"><i class="fa fa-file"></i> {{ __('general.browse') }}</a>
            </span>
        </div>
    </div>
</div>
<hr>
<div class="form-row">

    {{-- Porsi Kecil --}}
    <div class="col-md-6">
        <h5 class="mb-3">Porsi Kecil</h5>

        <div class="form-row">
            @foreach(['energi','lemak','protein','karbohidrat','serat'] as $field)
            <div class="form-group col-md-6">
                {!! Form::label('kecil_'.$field, ucfirst($field)) !!}
                
                {!! Form::number(
                    'kecil_'.$field,
                    old('kecil_'.$field, optional($menuharian)->{'kecil_'.$field}),
                    [
                        'class' => 'form-control',
                        'step' => '0.01',
                        'placeholder' => ucfirst($field)
                    ]
                ) !!}
            </div>
            @endforeach
        </div>
    </div>

    {{-- Porsi Besar --}}
    <div class="col-md-6">
        <h5 class="mb-3">Porsi Besar</h5>

        <div class="form-row">
            @foreach(['energi','lemak','protein','karbohidrat','serat'] as $field)
            <div class="form-group col-md-6">
                {!! Form::label('besar_'.$field, ucfirst($field)) !!}
                
                {!! Form::number(
                    'besar_'.$field,
                    old('besar_'.$field, optional($menuharian)->{'besar_'.$field}),
                    [
                        'class' => 'form-control',
                        'step' => '0.01',
                        'placeholder' => ucfirst($field)
                    ]
                ) !!}
            </div>
            @endforeach
        </div>
    </div>

</div>