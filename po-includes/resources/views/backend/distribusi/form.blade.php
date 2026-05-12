@php
	$isEdit = isset($distribusi) && $distribusi;
	$defaultSppgId = old('sppg_id', $distribusi->sppg_id ?? $sppgs->keys()->first());
@endphp

	<div class="card mg-b-20">
		<div class="card-header"><h6 class="mg-b-0">Data Distribusi</h6></div>
		<div class="card-body">
			<div class="form-row">
				<div class="form-group col-md-3">
					{!! Form::label('tanggal', 'Tanggal *', ['class' => 'control-label']) !!}
					{!! Form::date('tanggal', old('tanggal', $isEdit && $distribusi->tanggal ? $distribusi->tanggal->format('Y-m-d') : date('Y-m-d')), ['class' => $errors->has('tanggal') ? 'form-control is-invalid' : 'form-control', 'required']) !!}
					{!! $errors->first('tanggal', '<p class="text-danger">:message</p>') !!}
				</div>

				@if($isAdminDistribusi)
				<div class="form-group col-md-3">
					{!! Form::label('sppg_id', 'SPPG *', ['class' => 'control-label']) !!}
					<select name="sppg_id" id="sppg_id" class="form-control {{ $errors->has('sppg_id') ? 'is-invalid' : '' }}" required>
						<option value="">-- Pilih SPPG --</option>
						@foreach($sppgs as $id => $nama)
							<option value="{{ $id }}" {{ old('sppg_id', $distribusi->sppg_id ?? '') == $id ? 'selected' : '' }}>{{ $nama }}</option>
						@endforeach
					</select>
					{!! $errors->first('sppg_id', '<p class="text-danger">:message</p>') !!}
				</div>
				@endif

				@if($isSppgDistribusi)
					<input type="hidden" name="sppg_id" value="{{ Auth::user()->sppg_id }}">
				@endif

				@if($isSekolahDistribusi)
					<input type="hidden" name="sppg_id" value="{{ $defaultSppgId }}">
				@endif

				@if($isAdminDistribusi || $isSppgDistribusi)
				<div class="form-group col-md-3">
					{!! Form::label('sekolah_id', 'Sekolah *', ['class' => 'control-label']) !!}
					<select name="sekolah_id" class="form-control {{ $errors->has('sekolah_id') ? 'is-invalid' : '' }}" required>
						<option value="">-- Pilih Sekolah --</option>
						@foreach($sekolahs as $id => $nama)
							<option value="{{ $id }}" {{ old('sekolah_id', $distribusi->sekolah_id ?? '') == $id ? 'selected' : '' }}>{{ $nama }}</option>
						@endforeach
					</select>
					{!! $errors->first('sekolah_id', '<p class="text-danger">:message</p>') !!}
				</div>
				@endif

				@if($isSekolahDistribusi)
					<input type="hidden" name="sekolah_id" value="{{ Auth::user()->sekolah_id }}">
				@endif

				<div class="form-group col-md-3">
					{!! Form::label('menu_harian_id', 'Menu Harian', ['class' => 'control-label']) !!}
					<select name="menu_harian_id" class="form-control {{ $errors->has('menu_harian_id') ? 'is-invalid' : '' }}">
						<option value="">-- Tanpa Menu --</option>
						@foreach($menuHarians as $id => $nama)
							<option value="{{ $id }}" {{ old('menu_harian_id', $distribusi->menu_harian_id ?? '') == $id ? 'selected' : '' }}>{{ $nama }}</option>
						@endforeach
					</select>
					{!! $errors->first('menu_harian_id', '<p class="text-danger">:message</p>') !!}
				</div>

				<div class="form-group col-md-3">
					{!! Form::label('jumlah_porsi', 'Jumlah Porsi *', ['class' => 'control-label']) !!}
					{!! Form::number('jumlah_porsi', old('jumlah_porsi', $distribusi->jumlah_porsi ?? 0), ['class' => $errors->has('jumlah_porsi') ? 'form-control is-invalid' : 'form-control', 'min' => 0, 'required']) !!}
					{!! $errors->first('jumlah_porsi', '<p class="text-danger">:message</p>') !!}
				</div>

				@if($isEdit)
				<div class="form-group col-md-3">
					<label>Status Laporan</label>
					@php
						$statusDistribusi = [
							1 => ['Belum Lapor Sekolah', 'badge-warning'],
							2 => ['Sudah Lapor Sekolah', 'badge-info'],
						][(int) $distribusi->status_distribusi > 1 ? 2 : 1];
					@endphp
					<div><span class="badge {{ $statusDistribusi[1] }}">{{ $statusDistribusi[0] }}</span></div>
					<small class="text-muted">Status berubah otomatis saat sekolah mengupload laporan.</small>
				</div>
				@endif
			</div>
		</div>
	</div>

	<div class="card mg-b-20">
		<div class="card-header"><h6 class="mg-b-0">Keterangan</h6></div>
		<div class="card-body">
			{!! Form::textarea('keterangan', old('keterangan', $distribusi->keterangan ?? ''), ['class' => $errors->has('keterangan') ? 'form-control is-invalid' : 'form-control', 'rows' => 4]) !!}
			{!! $errors->first('keterangan', '<p class="text-danger">:message</p>') !!}
		</div>
	</div>
