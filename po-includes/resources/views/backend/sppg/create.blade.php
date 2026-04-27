@extends('layouts.app')
@section('title', __('Add SPPG'))

@section('content')
	<div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-20">
		<div>
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb breadcrumb-style1 mg-b-10">
					<li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ __('general.dashboard') }}</a></li>
					<li class="breadcrumb-item"><a href="#">MASTER</a></li>
					<li class="breadcrumb-item"><a href="{{ url('/dashboard/sppgs/table') }}">SPPG</a></li>
					<li class="breadcrumb-item active" aria-current="page">ADD SPPG</li>
				</ol>
			</nav>
			<h4 class="mg-b-0 tx-spacing--1">Add SPPG</h4>
		</div>
		
		<div><a href="{{ url('dashboard/sppgs/table') }}" class="btn btn-sm pd-x-15 btn-white btn-uppercase mg-t-10"><i data-feather="arrow-left" class="wd-10 mg-r-5"></i> {{ __('general.back') }}</a></div>
	</div>
	
	<div class="card">
		{!! Form::open(['url' => '/dashboard/sppgs', 'class' => 'form-horizontal']) !!}
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
				<button type="submit" class="btn btn-primary"><i data-feather="send" class="wd-10 mg-r-5"></i> {{ __('general.create') }}</button>
			</div>
		{!! Form::close() !!}
	</div>
@endsection

@push('scripts')
<script type="text/javascript">
	$('#parent_id, #wilayah_id').select2({
		placeholder: "Pilih data",
		allowClear: true
	});

	$('#sekolah_id').select2({
		placeholder: 'Pilih Sekolah',
		maximumSelectionLength: 10
	});

	$('#parent_id').on('change', function () {
		let id = $(this).val();

		$('#wilayah_id').html('<option>Loading...</option>');

		if (id) {
			$.get('/get-kelurahan/' + id, function (data) {
				let options = '<option value="">-- Pilih Kelurahan --</option>';

				$.each(data, function (id, nama) {
					options += `<option value="${id}">${nama}</option>`;
				});

				$('#wilayah_id').html(options);
			});
		} else {
			$('#wilayah_id').html('<option value="">-- Pilih Kelurahan --</option>');
		}
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