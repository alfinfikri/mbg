@extends('layouts.app')
@section('title', 'Add Daily Menu')

@section('content')
	<div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-20">
		<div>
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb breadcrumb-style1 mg-b-10">
					<li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ __('general.dashboard') }}</a></li>
					<li class="breadcrumb-item"><a href="#">MASTER</a></li>
					<li class="breadcrumb-item"><a href="{{ url('/dashboard/menu-harians/table') }}">Daily Menu</a></li>
					<li class="breadcrumb-item active" aria-current="page">ADD Daily Menu</li>
				</ol>
			</nav>
			<h4 class="mg-b-0 tx-spacing--1">Add Daily Menu</h4>
		</div>
		
		<div><a href="{{ url('dashboard/menu-harians/table') }}" class="btn btn-sm pd-x-15 btn-white btn-uppercase mg-t-10"><i data-feather="arrow-left" class="wd-10 mg-r-5"></i> {{ __('general.back') }}</a></div>
	</div>
	
	{!! Form::open(['url' => '/dashboard/menu-harians', 'class' => 'form-horizontal', 'files' => true]) !!}
		@if ($errors->any())
			<ul class="alert alert-danger">
				@foreach ($errors->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
			</ul>
		@endif

		@include ('backend.menuharian.form')

		<div class="mg-t-20">
			<button type="submit" class="btn btn-primary"><i data-feather="send" class="wd-10 mg-r-5"></i> {{ __('general.create') }}</button>
		</div>
	{!! Form::close() !!}
@endsection
