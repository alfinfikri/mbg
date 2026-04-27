<div class="form-row">
	<div class="form-group col-md-6">
		{!! Form::label('username', __('user.username').' *', ['class' => 'control-label']) !!}
		<input type="text" class="form-control" value="{{ isset($user) ? $user->username : __('user.auto') }}" disabled />
	</div>
	<div class="form-group col-md-6">
		{!! Form::label('name', __('user.name').' *', ['class' => 'control-label']) !!}
		{!! Form::text('name', null, ['class' => $errors->has('name') ? 'form-control is-invalid' : 'form-control', 'required' => 'required']) !!}
		{!! $errors->first('name', '<p class="help-block">:message</p>') !!}
	</div>
</div>
<div class="form-row">
	<div class="form-group col-md-4">
		{!! Form::label('email', __('user.email').' *', ['class' => 'control-label']) !!}
		{!! Form::email('email', null, ['class' => $errors->has('email') ? 'form-control is-invalid' : 'form-control', 'required' => 'required']) !!}
		{!! $errors->first('email', '<p class="help-block">:message</p>') !!}
	</div>
	<div class="form-group col-md-4">
		{!! Form::label('telp', __('user.telephone'), ['class' => 'control-label']) !!}
		{!! Form::text('telp', null, ['class' => $errors->has('telp') ? 'form-control is-invalid' : 'form-control']) !!}
		{!! $errors->first('telp', '<p class="help-block">:message</p>') !!}
	</div>
	<div class="form-group col-md-4">
		{!! Form::label('password', __('user.password').' *', ['class' => 'control-label']) !!}
		@php
			$passwordOptions = ['class' => $errors->has('password') ? 'form-control is-invalid' : 'form-control'];
			if ($formMode === 'create') {
				$passwordOptions = array_merge($passwordOptions, ['required' => 'required']);
			}
		@endphp
		{!! Form::password('password', $passwordOptions) !!}
		{!! $errors->first('password', '<p class="help-block">:message</p>') !!}
		<small><i>{{ (isset($user) ? __('user.password_info') : '') }}</i></small>
	</div>
</div>
@if (isset($user))
	@if (Auth::user()->id == $user->id)
	<div class="form-group">
		{!! Form::label('picture', __('user.picture'), ['class' => 'control-label']) !!}
		<div class="input-group">
			{!! Form::text('picture', null, ['class' => $errors->has('picture') ? 'form-control is-invalid' : 'form-control', 'id' => 'input-filemanager']) !!}
			<span class="input-group-append">
				<a href="{{ url('po-content/cms-pemkot/file/dialog.php?type=1&field_id=input-filemanager&relative_url=1&akey='.config('fm.key')) }}" id="filemanager" class="btn btn-secondary"><i class="fa fa-file"></i> {{ __('general.browse') }}</a>
			</span>
		</div>
		{!! $errors->first('picture', '<p class="help-block">:message</p>') !!}
	</div>
	@endif
@endif
@if (Auth::user()->hasRole('superadmin') || Auth::user()->hasRole('admin'))
	<div class="form-row">
		<div class="form-group col-md-6">
			{!! Form::label('block', __('user.block').' *', ['class' => 'control-label']) !!}
			<select class="select-style form-control" id="block" name="block">
				@if (isset($user))
					<option value="{{ $user->block }}">{{ __('general.selected') }} {{ $user->block == 'Y' ? __('user.block') : __('user.unblock') }}</option>
				@endif
				<option value="Y">{{ __('user.block') }}</option>
				<option value="N">{{ __('user.unblock') }}</option>
			</select>
			{!! $errors->first('block', '<p class="help-block">:message</p>') !!}
		</div>
		{{-- <div class="form-group col-md-6">
			{!! Form::label('roles', __('user.role').' *', ['class' => 'control-label']) !!}
			<select name="roles[]" class="select-style form-control roles" multiple required>
				@foreach ($roles as $row)
					@if (isset($user))
						<option value="{{ $row->name }}" {{ $user->hasRole($row) ? 'selected':'' }}>{{ $row->name }}</option>
					@else
						<option value="{{ $row->name }}">{{ $row->name }}</option>
					@endif
				@endforeach
			</select>
		</div> --}}
		<div class="form-group col-md-6">
			{!! Form::label('roles', __('user.role').' *', ['class' => 'control-label']) !!}
			<select name="roles" id="roles" class="select-style form-control roles" required>
				<option value=""> pilih akses </option>
				@foreach ($roles as $row)
					@if (isset($user))
						<option value="{{ $row->name }}" {{ $user->hasRole($row) ? 'selected':'' }}>{{ $row->name }}</option>
					@else
						<option value="{{ $row->name }}">{{ $row->name }}</option>
					@endif
				@endforeach
			</select>
		</div>
		<div class="form-group col-md-12" id="sppg-wrapper" style="display:none;">
			{!! Form::label('sppg_id', 'SPPG', ['class' => 'control-label']) !!}
			
			<select name="sppg_id" class="form-control">
				<option value="">-- Pilih SPPG --</option>

				@foreach($sppgs as $id => $nama)
					<option value="{{ $id }}"
						{{ old('sppg_id', $user->sppg_id ?? '') == $id ? 'selected' : '' }}>
						{{ $nama }}
					</option>
				@endforeach
			</select>
		</div>
	</div>
@endif
