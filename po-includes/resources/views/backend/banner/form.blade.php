<div class="form-group">
	{!! Form::label('gambar', 'Gambar *', ['class' => 'control-label']) !!}
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
	 <input class="date form-control" type="text" name="tanggal" value="{{ !empty($banner->tanggal) ? $banner->tanggal : null }}" placeholder="{{ date('Y-m-d') }}" onkeydown="return false" required>
	</div>
	{!! $errors->first('tanggal', '<p class="help-block">:message</p>') !!}
</div>
<div class="form-group">
	{!! Form::label('keterangan', 'Keterangan', ['class' => 'control-label']) !!}
	{!! Form::textarea('keterangan', null, ['class' => $errors->has('keterangan') ? 'form-control is-invalid ht-150-i' : 'form-control ht-150-i']) !!}
	{!! $errors->first('keterangan', '<p class="help-block">:message</p>') !!}
</div>