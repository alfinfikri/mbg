@extends('layouts.app')
@section('title', __('banner.show_title'))

@section('content')
	<div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-20">
		<div>
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb breadcrumb-style1 mg-b-10">
					<li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ __('general.dashboard') }}</a></li>
					<li class="breadcrumb-item"><a href="{{ url('/dashboard/viewpage/table') }}">{{ __('general.viewpage') }}</a></li>
					<li class="breadcrumb-item"><a href="{{ url('/dashboard/viewpage/table') }}">{{ __('general.viewpage') }}</a></li>
					<li class="breadcrumb-item active" aria-current="page">{{ __('viewpage.datatable_list') }}</li>
				</ol>
			</nav>
			<h4 class="mg-b-0 tx-spacing--1">{{ __('viewpage.show_title') }}</h4>
		</div>
		
		<div><a href="{{ url('dashboard/viewpage/table') }}" class="btn btn-sm pd-x-15 btn-white btn-uppercase mg-t-10"><i data-feather="arrow-left" class="wd-10 mg-r-5"></i> {{ __('general.back') }}</a></div>
	</div>
	
	<div class="card">
		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-striped">
					<tbody>
						<tr>
							<th>{{ __('viewpage.device') }}</th><td>{{ $viewpage->device }}</td>
						</tr>
						<tr>
							<th>{{ __('viewpage.platform') }}</th><td>{{ $viewpage->platform }}</td>
						</tr>
						<tr>
							<th>{{ __('viewpage.browser') }}</th><td>{{ $viewpage->browser }}</td>
						</tr>
						<tr>
							<th>{{ __('viewpage.ip') }}</th><td>{{ $viewpage->ip }}</td>
						</tr>
						<tr>
							<th>{{ __('viewpage.created_at') }}</th><td> {{ Carbon\Carbon::parse($viewpage->created_at)->isoFormat('D MMMM Y') }}</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
@endsection
