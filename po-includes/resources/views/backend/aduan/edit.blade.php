@extends('layouts.app')
@section('title', __('Edit Delivery'))

@section('content')
	<div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-20">
		<div>
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb breadcrumb-style1 mg-b-10">
					<li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ __('general.dashboard') }}</a></li>
					<li class="breadcrumb-item"><a href="#">MASTER</a></li>
					<li class="breadcrumb-item"><a href="{{ url('/dashboard/deliverys/table') }}">DELIVERY</a></li>
					<li class="breadcrumb-item active" aria-current="page">Edit DELIVERY</li>
				</ol>
			</nav>
			<h4 class="mg-b-0 tx-spacing--1">Edit Delivery</h4>
		</div>
		
		<div><a href="{{ url('dashboard/deliverys/table') }}" class="btn btn-sm pd-x-15 btn-white btn-uppercase mg-t-10"><i data-feather="arrow-left" class="wd-10 mg-r-5"></i> {{ __('general.back') }}</a></div>
	</div>
	
	<div class="card">
		{!! Form::model($delivery, [
			'method' => 'PATCH',
			'url' => ['/dashboard/deliverys', Hashids::encode($delivery->id)],
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
				
				@include ('backend.delivery.form')
			</div>
			<div class="card-footer">
				<button type="submit" class="btn btn-primary"><i data-feather="send" class="wd-10 mg-r-5"></i> {{ __('general.update') }}</button>
			</div>
		{!! Form::close() !!}
	</div>
@endsection

@push('scripts')
<script>
let porsiData = @json($porsi ?? []);

function loadSekolah(sppg_id) {
    $('#list-sekolah').html('Loading...');

    $.get('/dashboard/deliverys/get-sekolah-by-sppg/' + sppg_id, function (data) {

        let html = '';

        data.forEach(function (item) {

            let value = porsiData[item.id] ?? '';

            html += `
                <div class="form-row mb-2">
                    <div class="col-md-6">
                        <input type="text" class="form-control" value="${item.nama}" readonly>
                    </div>
                    <div class="col-md-6">
                        <input type="number" 
                               name="porsi[${item.id}]" 
                               class="form-control" 
                               value="${value}" 
                               placeholder="Jumlah Porsi" required>
                    </div>
                </div>
            `;
        });

        $('#list-sekolah').html(html);
    });
}

// 🔥 load saat edit pertama kali
$(document).ready(function () {
    let sppg_id = $('#sppg_id').val();
    if (sppg_id) {
        loadSekolah(sppg_id);
    }
});

// 🔥 load saat ganti sppg
$('#sppg_id').on('change', function () {
    loadSekolah($(this).val());
});
</script>
@endpush
