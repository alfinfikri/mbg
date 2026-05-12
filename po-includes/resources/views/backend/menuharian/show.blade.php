@extends('layouts.app')
@section('title', 'Detail Daily Menu')

@php
    $smallFields = [
        'kecil_energi' => 'Energi',
        'kecil_lemak' => 'Lemak',
        'kecil_protein' => 'Protein',
        'kecil_karbohidrat' => 'Karbohidrat',
        'kecil_serat' => 'Serat',
    ];

    $largeFields = [
        'besar_energi' => 'Energi',
        'besar_lemak' => 'Lemak',
        'besar_protein' => 'Protein',
        'besar_karbohidrat' => 'Karbohidrat',
        'besar_serat' => 'Serat',
    ];
@endphp

@section('content')
	<div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-20">
		<div>
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb breadcrumb-style1 mg-b-10">
					<li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ __('general.dashboard') }}</a></li>
					<li class="breadcrumb-item"><a href="#">MASTER</a></li>
					<li class="breadcrumb-item"><a href="{{ url('/dashboard/menu-harians/table') }}">Daily Menu</a></li>
					<li class="breadcrumb-item active" aria-current="page">Detail Daily Menu</li>
				</ol>
			</nav>
			<h4 class="mg-b-0 tx-spacing--1">Detail Daily Menu</h4>
		</div>

		<div>
            <a href="{{ url('dashboard/menu-harians/table') }}" class="btn btn-sm pd-x-15 btn-white btn-uppercase mg-t-10"><i data-feather="arrow-left" class="wd-10 mg-r-5"></i> {{ __('general.back') }}</a>
            @if(Auth::user()->can('update-menu-harians') && (Auth::user()->hasRole('superadmin') || Auth::user()->hasRole('superadmin 2') || Auth::user()->hasRole('admin') || Auth::user()->sppg_id == $menuharian->sppg_id))
                <a href="{{ url('dashboard/menu-harians/'.Hashids::encode($menuharian->id).'/edit') }}" class="btn btn-sm pd-x-15 btn-primary btn-uppercase mg-t-10"><i data-feather="edit" class="wd-10 mg-r-5"></i> {{ __('general.edit') }}</a>
            @endif
        </div>
	</div>

	<div class="card">
		<div class="card-body">
            @if($menuharian->foto)
                <div class="mg-b-20">
                    <img src="{{ asset('po-content/uploads/'.$menuharian->foto) }}" class="img-fluid rounded" style="max-height:320px;" alt="">
                </div>
            @endif

			<div class="table-responsive">
				<table class="table table-striped">
					<tbody>
						<tr>
							<th style="width:200px;">Tanggal</th>
                            <td>{{ $menuharian->tanggal ? $menuharian->tanggal->format('d-m-Y') : '-' }}</td>
						</tr>
						<tr>
							<th>Nama Menu</th>
                            <td>{{ $menuharian->nama }}</td>
						</tr>
						<tr>
							<th>SPPG</th>
                            <td>{{ optional($menuharian->sppg)->nama ?? '-' }}</td>
						</tr>
						<tr>
							<th>Deskripsi</th>
                            <td>{{ $menuharian->deskripsi ?: '-' }}</td>
						</tr>
						<tr>
							<th>Created By</th>
                            <td>{{ optional($menuharian->createdBy)->name ?? '-' }}</td>
						</tr>
						<tr>
							<th>Updated By</th>
                            <td>{{ optional($menuharian->updatedBy)->name ?? '-' }}</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>

    <div class="row mg-t-20">
        <div class="col-md-6">
            @include('backend.menuharian.partials.gizi-table', ['title' => 'Nilai Gizi Porsi Kecil', 'fields' => $smallFields])
        </div>
        <div class="col-md-6">
            @include('backend.menuharian.partials.gizi-table', ['title' => 'Nilai Gizi Porsi Besar', 'fields' => $largeFields])
        </div>
    </div>

    @if(Auth::user()->can('update-menu-harians') && (Auth::user()->hasRole('superadmin') || Auth::user()->hasRole('superadmin 2') || Auth::user()->hasRole('admin') || Auth::user()->sppg_id == $menuharian->sppg_id))
    <div class="mg-t-20">
        {!! Form::open(['url' => 'dashboard/menu-harians/'.Hashids::encode($menuharian->id).'/distribusi', 'method' => 'post']) !!}
            @include('backend.menuharian.partials.distribusi-sekolah')
            <button type="submit" class="btn btn-primary">
                <i data-feather="save" class="wd-10 mg-r-5"></i> Simpan Distribusi
            </button>
        {!! Form::close() !!}
    </div>
    @endif
@endsection
