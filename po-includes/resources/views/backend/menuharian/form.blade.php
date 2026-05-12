<div class="card mg-b-20">
    <div class="card-header">
        <h6 class="mg-b-0">Data Utama Menu</h6>
    </div>
    <div class="card-body">
        @php
            $isAdminMenuHarian = Auth::user()->hasRole('superadmin') || Auth::user()->hasRole('superadmin 2') || Auth::user()->hasRole('admin');
            $authSppgId = Auth::user()->sppg_id;
        @endphp
        <div class="form-row">
            @if($isAdminMenuHarian)
            <div class="form-group col-md-12">
                {!! Form::label('sppg_id', 'SPPG', ['class' => 'control-label']) !!}
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

            @if(!$isAdminMenuHarian && $authSppgId)
                <input type="hidden" name="sppg_id" value="{{ $authSppgId }}">
            @endif

            <div class="form-group col-md-3">
                {!! Form::label('tanggal', 'Tanggal *', ['class' => 'control-label']) !!}
                {!! Form::date('tanggal', old('tanggal', isset($menuharian) && $menuharian->tanggal ? $menuharian->tanggal->format('Y-m-d') : ''), [
                    'class' => $errors->has('tanggal') ? 'form-control is-invalid' : 'form-control',
                    'required'
                ]) !!}
                {!! $errors->first('tanggal', '<p class="text-danger">:message</p>') !!}
            </div>

            <div class="form-group col-md-9">
                {!! Form::label('nama', 'Nama Menu *', ['class' => 'control-label']) !!}
                {!! Form::text('nama', old('nama', $menuharian->nama ?? ''), [
                    'class' => $errors->has('nama') ? 'form-control is-invalid' : 'form-control',
                    'required'
                ]) !!}
                {!! $errors->first('nama', '<p class="text-danger">:message</p>') !!}
            </div>

            <div class="form-group col-md-6">
                {!! Form::label('deskripsi', 'Deskripsi', ['class' => 'control-label']) !!}
                {!! Form::textarea('deskripsi', old('deskripsi', $menuharian->deskripsi ?? ''), [
                    'class' => $errors->has('deskripsi') ? 'form-control is-invalid' : 'form-control',
                    'rows' => 3,
                    'placeholder' => 'Contoh: Nasi, Telur, Sayur, Jeruk'
                ]) !!}
                {!! $errors->first('deskripsi', '<p class="text-danger">:message</p>') !!}
            </div>

            <div class="form-group col-md-6">
                {!! Form::label('foto', 'Foto Menu (.jpg/.jpeg/.png)', ['class' => 'control-label']) !!}
                {!! Form::file('foto', [
                    'class' => $errors->has('foto') ? 'form-control is-invalid' : 'form-control',
                    'accept' => 'image/jpeg,image/png'
                ]) !!}
                {!! $errors->first('foto', '<p class="text-danger">:message</p>') !!}

                @if(isset($menuharian) && $menuharian->foto)
                    <div class="mg-t-10">
                        <img src="{{ asset('po-content/uploads/'.$menuharian->foto) }}" class="img-fluid rounded" style="max-height:160px;" alt="">
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@foreach(['kecil' => 'Nilai Gizi Porsi Kecil', 'besar' => 'Nilai Gizi Porsi Besar'] as $prefix => $title)
<div class="card mg-b-20">
    <div class="card-header">
        <h6 class="mg-b-0">{{ $title }}</h6>
    </div>
    <div class="card-body">
        <div class="form-row">
            @foreach(['energi' => 'Energi', 'lemak' => 'Lemak', 'protein' => 'Protein', 'karbohidrat' => 'Karbohidrat', 'serat' => 'Serat'] as $field => $label)
            <div class="form-group col-md-4">
                {!! Form::label($prefix.'_'.$field, $label, ['class' => 'control-label']) !!}
                {!! Form::number(
                    $prefix.'_'.$field,
                    old($prefix.'_'.$field, optional($menuharian)->{$prefix.'_'.$field}),
                    [
                        'class' => $errors->has($prefix.'_'.$field) ? 'form-control is-invalid' : 'form-control',
                        'step' => '0.01',
                        'min' => 0,
                        'placeholder' => $label
                    ]
                ) !!}
                {!! $errors->first($prefix.'_'.$field, '<p class="text-danger">:message</p>') !!}
            </div>
            @endforeach
        </div>
    </div>
</div>
@endforeach

@include('backend.menuharian.partials.distribusi-sekolah')
