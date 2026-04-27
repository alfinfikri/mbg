@extends('layouts.app')
@section('title', __('Cetak Halaman Visitor'))

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.css" rel="stylesheet" type="text/css" />
@endpush

@section('content')
	<div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-20">
		<div>
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb breadcrumb-style1 mg-b-10">
					<li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ __('general.dashboard') }}</a></li>
					<li class="breadcrumb-item"><a href="{{ url('/dashboard/viewpage/table') }}"> Halaman Pengunjung </a></li>
					<li class="breadcrumb-item"><a href="{{ url('/dashboard/viewpage/table') }}"> Halaman Pengunjung </a></li>
					<li class="breadcrumb-item active" aria-current="page"> Cetak Halaman Visitor </li>
				</ol>
			</nav>
			<h4 class="mg-b-0 tx-spacing--1"> {{ __('viewpage.cetak') }} </h4>
		</div>
		
		<div><a href="{{ url('dashboard/viewpage/table') }}" class="btn btn-sm pd-x-15 btn-white btn-uppercase mg-t-10"><i data-feather="arrow-left" class="wd-10 mg-r-5"></i> {{ __('general.back') }} </a></div>
	</div>
	
	<div class="card">
		{!! Form::open(['url' => '/dashboard/viewpage/cetak_aksi', 'class' => 'form-horizontal', 'method' => 'GET']) !!}
			<div class="card-body pd-b-0">
				@if ($errors->any())
					<ul class="alert alert-danger">
						@foreach ($errors->all() as $error)
							<li>{{ $error }}</li>
						@endforeach
					</ul>
				@endif
				
				@include('backend.viewpage.form_cetak', ['formMode' => 'cetak'])
			</div>
			<div class="card-footer">
				<button type="submit" class="btn btn-primary"><i data-feather="send" class="wd-10 mg-r-5"></i> Cetak </button>
			</div>
		{!! Form::close() !!}
	</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.js"></script>
<script type="text/javascript">
    $('.date').datepicker({  
       format: 'yyyy-mm-dd'
     });  
</script>
@endpush
