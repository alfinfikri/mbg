@extends('layouts.app')
@section('title', 'Tambah Laporan Sekolah')

@section('content')
	<div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-20">
		<div>
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb breadcrumb-style1 mg-b-10">
					<li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ __('general.dashboard') }}</a></li>
					<li class="breadcrumb-item"><a href="{{ url('/dashboard/laporan-sekolahs/table') }}">Laporan Sekolah</a></li>
					<li class="breadcrumb-item active" aria-current="page">Tambah</li>
				</ol>
			</nav>
			<h4 class="mg-b-0 tx-spacing--1">Tambah Laporan Sekolah</h4>
		</div>
		<div><a href="{{ url('dashboard/laporan-sekolahs/table') }}" class="btn btn-sm pd-x-15 btn-white btn-uppercase mg-t-10"><i data-feather="arrow-left" class="wd-10 mg-r-5"></i> {{ __('general.back') }}</a></div>
	</div>

	{!! Form::open(['url' => '/dashboard/laporan-sekolahs', 'class' => 'form-horizontal', 'files' => true]) !!}
		@include('backend.laporansekolah.form')
		<button type="submit" class="btn btn-primary">{{ __('general.create') }}</button>
	{!! Form::close() !!}
@endsection
