<div class="form-row">
	<div class="form-group col-md-12">
		{!! Form::label('isitext', __('runningtext.isitext').' *', ['class' => 'control-label']) !!}
		{!! Form::textarea('isitext', null, ['class' => $errors->has('isitext') ? 'form-control is-invalid' : 'form-control', 'required' => 'required']) !!}
		{!! $errors->first('isitext', '<p class="help-block">:message</p>') !!}
	</div>
</div>
