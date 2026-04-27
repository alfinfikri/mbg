@extends('layouts.app')
@section('title', __('gallery.show_title'))

@section('content')
	<div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-20">
		<div>
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb breadcrumb-style1 mg-b-10">
					<li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ __('general.dashboard') }}</a></li>
					<li class="breadcrumb-item"><a href="{{ url('/dashboard/components/table') }}">{{ __('general.components') }}</a></li>
					<li class="breadcrumb-item"><a href="{{ url('/dashboard/infografis/table') }}">{{ __('general.infografis') }}</a></li>
					<li class="breadcrumb-item active" aria-current="page">{{ __('infografis.show_title') }}</li>
				</ol>
			</nav>
			<h4 class="mg-b-0 tx-spacing--1">{{ __('infografis.show_title') }}</h4>
		</div>
		
		<div><a href="{{ url('dashboard/infografis/table') }}" class="btn btn-sm pd-x-15 btn-white btn-uppercase mg-t-10"><i data-feather="arrow-left" class="wd-10 mg-r-5"></i> {{ __('general.back') }}</a></div>
	</div>
	
	<div class="card">
		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-striped">
					<tbody>
						<tr>
							<th style="width:180px;">{{ __('infografis.gambar') }}</th><td>{{ $infografis->gambar }}</td>
						</tr>
						<tr>
							<th>{{ __('infografis.keterangan') }}</th><td>{{ $infografis->keterangan }}</td>
						</tr>
						<tr>
							<th>{{ __('infografis.gambar') }}</th>
							<td>
								@if($infografis->gambar != '')
									<a href="{{ asset('po-content/uploads/'.$infografis->gambar) }}" target="_blank">{{ __('gallery.see_image') }}</a>
								@endif
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
@endsection
