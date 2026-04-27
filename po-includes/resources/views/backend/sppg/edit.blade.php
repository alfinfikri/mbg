@extends('layouts.app')
@section('title', __('Edit SPPG'))

@section('content')
	<div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-20">
		<div>
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb breadcrumb-style1 mg-b-10">
					<li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ __('general.dashboard') }}</a></li>
					<li class="breadcrumb-item"><a href="#">MASTER</a></li>
					<li class="breadcrumb-item"><a href="{{ url('/dashboard/sppgs/table') }}">SPPG</a></li>
					<li class="breadcrumb-item active" aria-current="page">Edit SPPG</li>
				</ol>
			</nav>
			<h4 class="mg-b-0 tx-spacing--1">Edit SPPG</h4>
		</div>
		
		<div><a href="{{ url('dashboard/sppgs/table') }}" class="btn btn-sm pd-x-15 btn-white btn-uppercase mg-t-10"><i data-feather="arrow-left" class="wd-10 mg-r-5"></i> {{ __('general.back') }}</a></div>
	</div>
	
	<div class="card">
		{!! Form::model($sppg, [
			'method' => 'PATCH',
			'url' => ['/dashboard/sppgs', Hashids::encode($sppg->id)],
			'class' => 'form-horizontal'
		]) !!}
			<div class="card-body pd-b-0">
				@if ($errors->any())
					<ul class="alert alert-danger">
						@foreach ($errors->all() as $error)
							<li>{{ $error }}</li>
						@endforeach
					</ul>
				@endif
				
				@include ('backend.sppg.form')
			</div>
			<div class="card-footer">
				<button type="submit" class="btn btn-primary"><i data-feather="send" class="wd-10 mg-r-5"></i> {{ __('general.update') }}</button>
			</div>
		{!! Form::close() !!}
	</div>
@endsection

@push('scripts')
<script>
	$('#parent_id, #wilayah_id').select2({
		placeholder: "Pilih data",
		allowClear: true
	});

	$('#sekolah_id').select2({
		placeholder: 'Pilih Sekolah',
		maximumSelectionLength: 10
	});
	
	$(document).ready(function () {

		let selectedWilayah = "{{ old('wilayah_id', $sppg->wilayah_id ?? '') }}";
		let selectedKecamatan = $('#parent_id').val();

		function loadKelurahan(kecamatanId, selected = null) {
			if (!kecamatanId) return;

			$('#wilayah_id').html('<option>Loading...</option>');

			$.get('/get-kelurahan/' + kecamatanId, function (data) {
				let options = '<option value="">-- Pilih Kelurahan --</option>';

				$.each(data, function (id, nama) {
					let isSelected = (selected == id) ? 'selected' : '';
					options += `<option value="${id}" ${isSelected}>${nama}</option>`;
				});

				$('#wilayah_id').html(options);
			});
		}

		// AUTO LOAD saat edit
		if (selectedKecamatan) {
			loadKelurahan(selectedKecamatan, selectedWilayah);
		}

		// Ganti kecamatan
		$('#parent_id').on('change', function () {
			loadKelurahan($(this).val());
		});

	});

	$('#sekolah_id').select2({
		placeholder: 'Pilih Sekolah',
		multiple: true,
		ajax: {
			url: '{{ url("dashboard/sekolahs/get-sekolahs") }}',
			dataType: 'json',
			delay: 250,
			data: function (params) {
				return {
					term: params.term
				};
			},
			processResults: function (data) {
				return {
					results: $.map(data.data, function (item) {
						return {
							id: item.id,
							text: item.nama
						};
					})
				};
			}
		}
	});
</script>
@endpush
