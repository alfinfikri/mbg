<div class="form-group">
	{!! Form::label('Tanggal Awal', __('Tanggal Awal').' *', ['class' => 'control-label']) !!}
	<div class="input-group">
		<input class="date form-control" type="text" name="start_date" placeholder="{{ date('Y-m-d') }}" required>
	</div>
</div>
<div class="form-group">
	{!! Form::label('Tanggal Akhir', __('Tanggal Akhir'), ['class' => 'control-label']) !!}
	<div class="input-group">
		<input class="date form-control" type="text" name="end_date" placeholder="{{ date('Y-m-d') }}" required>
	</div>
</div>