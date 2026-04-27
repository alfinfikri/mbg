<div class="form-row">

    {{-- SPPG --}}
    @if(!Auth::user()->hasRole('sppg'))
    <div class="form-group col-md-6">
        {!! Form::label('sppg_id', 'SPPG *', ['class' => 'control-label']) !!}
        {!! Form::select('sppg_id', $sppgs, old('sppg_id', $delivery->sppg_id ?? ''), [
            'class' => $errors->has('sppg_id') ? 'form-control is-invalid' : 'form-control',
            'placeholder' => '-- Pilih SPPG --',
            'required'
        ]) !!}
        {!! $errors->first('sppg_id', '<p class="text-danger">:message</p>') !!}
    </div>
    @endif
    @if(Auth::user()->hasRole('sppg'))
    <input type="hidden" id="sppg_id" name="sppg_id" value="{{ Auth::user()->sppg_id }}">
    @endif

    {{-- Menu --}}
    <div class="form-group col-md-6">
        {!! Form::label('menu_id', 'Menu Harian *') !!}
        
        {!! Form::select('menu_id', $menus, old('menu_id', $delivery->menu_id ?? ''), [
            'class' => $errors->has('menu_id') ? 'form-control is-invalid' : 'form-control',
            'placeholder' => '-- Pilih Menu Hari Ini --',
            'required'
        ]) !!}
        
        {!! $errors->first('menu_id', '<p class="text-danger">:message</p>') !!}
    </div>

    {{-- Tanggal --}}
    <div class="form-group col-md-6">
        {!! Form::label('tanggal', 'Tanggal *', ['class' => 'control-label']) !!}
        {!! Form::date('tanggal', old('tanggal', $delivery->tanggal ?? ''), [
            'class' => $errors->has('tanggal') ? 'form-control is-invalid' : 'form-control',
            'required'
        ]) !!}
        {!! $errors->first('tanggal', '<p class="text-danger">:message</p>') !!}
    </div>

    {{-- Foto --}}
    <div class="form-group col-md-12">
        {!! Form::label('foto','Bukti Foto Pengiriman', ['class' => 'control-label']) !!}
        <div class="input-group">
            {!! Form::text('foto', null, ['class' => $errors->has('foto') ? 'form-control is-invalid' : 'form-control', 'id' => 'input-filemanager']) !!}
            <span class="input-group-append">
                <a href="{{ url('po-content/cms-pemkot/file/dialog.php?type=1&field_id=input-filemanager&relative_url=1&akey='.config('fm.key')) }}" id="filemanager" class="btn btn-secondary"><i class="fa fa-file"></i> {{ __('general.browse') }}</a>
            </span>
        </div>
    </div>
    

    {{-- Sekolah (MULTIPLE) --}}
    <div class="form-group col-md-12">
        <label>Sekolah & Jumlah Porsi</label>

        <div id="list-sekolah">
            {{-- akan diisi via ajax --}}
        </div>
    </div>

</div>