<div class="form-group">
	{!! Form::hidden('seotitle', null, ['id' => 'seotitle']) !!}
	{!! Form::label('title', __('album.title').' *', ['class' => 'control-label']) !!}
	{!! Form::text('title', null, ['class' => $errors->has('title') ? 'form-control is-invalid' : 'form-control', 'required' => 'required']) !!}
	{!! $errors->first('title', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group">
	{!! Form::label('gambar', __('album.gambar').' *', ['class' => 'control-label']) !!}
	<div class="input-group">
		{!! Form::text('gambar', null, ['class' => $errors->has('gambar') ? 'form-control is-invalid' : 'form-control', 'id' => 'input-filemanager', 'required' => 'required']) !!}
		<span class="input-group-append">
		   <a href="{{ url('po-content/cms-pemkot/file/dialog.php?type=1&field_id=input-filemanager&relative_url=1&akey='.config('fm.key')) }}" id="filemanager" class="btn btn-secondary"><i class="fa fa-file"></i> {{ __('general.browse') }}</a>
		</span>
	</div>
</div>
<div class="form-group">
	{!! Form::label('tanggal', 'Tanggal *', ['class' => 'control-label']) !!}
	<div class="input-group">
	 <input class="date form-control" type="text" name="tanggal" value="{{ !empty($album->tanggal) ? $album->tanggal : null }}" placeholder="{{ date('Y-m-d') }}" onkeydown="return false" required>
	</div>
	{!! $errors->first('tanggal', '<p class="help-block">:message</p>') !!}
</div>
@if($formMode == 'edit')
<div class="form-group">
	{!! Form::label('active', __('album.active').' *', ['class' => 'control-label']) !!}
	<select class="select-style form-control" id="active" name="active">
		<option value="{{ $album->active }}">{{ __('general.selected') }} {{ $album->active == 'Y' ? __('album.active') : __('album.deactive') }}</option>
		<option value="Y">{{ __('album.active') }}</option>
		<option value="N">{{ __('album.deactive') }}</option>
	</select>
	{!! $errors->first('active', '<p class="help-block">:message</p>') !!}
</div>
@endif