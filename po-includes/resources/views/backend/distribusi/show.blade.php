@extends('layouts.app')
@section('title', 'Detail Distribusi MBG')

@section('content')
	<div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-20">
		<div>
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb breadcrumb-style1 mg-b-10">
					<li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ __('general.dashboard') }}</a></li>
					<li class="breadcrumb-item"><a href="{{ url('/dashboard/distribusis/table') }}">Distribusi MBG</a></li>
					<li class="breadcrumb-item active" aria-current="page">Detail</li>
				</ol>
			</nav>
			<h4 class="mg-b-0 tx-spacing--1">Detail Distribusi MBG</h4>
		</div>

		<div>
			<a href="{{ url('dashboard/distribusis/table') }}" class="btn btn-sm pd-x-15 btn-white btn-uppercase mg-t-10"><i data-feather="arrow-left" class="wd-10 mg-r-5"></i> {{ __('general.back') }}</a>
			<a href="{{ url('dashboard/distribusis/'.Hashids::encode($distribusi->id).'/edit') }}" class="btn btn-sm pd-x-15 btn-primary btn-uppercase mg-t-10"><i data-feather="edit" class="wd-10 mg-r-5"></i> {{ __('general.edit') }}</a>
		</div>
	</div>

	<div class="card">
		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-striped">
					<tbody>
						<tr><th style="width:220px;">Tanggal</th><td>{{ optional($distribusi->tanggal)->format('d-m-Y') ?: '-' }}</td></tr>
						<tr><th>SPPG</th><td>{{ optional($distribusi->sppg)->nama ?? '-' }}</td></tr>
						<tr><th>Sekolah</th><td>{{ optional($distribusi->sekolah)->nama ?? '-' }}</td></tr>
						<tr><th>Menu Harian</th><td>{{ optional($distribusi->menuHarian)->nama ?? '-' }}</td></tr>
						<tr><th>Jumlah Porsi</th><td>{{ number_format($distribusi->jumlah_porsi, 0, ',', '.') }}</td></tr>
						<tr>
							<th>Status Laporan</th>
							<td>
								@php
									$status = [
										1 => ['Belum Lapor Sekolah', 'badge-warning'],
										2 => ['Sudah Lapor Sekolah', 'badge-info'],
									][(int) $distribusi->status_distribusi > 1 ? 2 : 1];
								@endphp
								<span class="badge {{ $status[1] }}">{{ $status[0] }}</span>
							</td>
						</tr>
						<tr><th>Keterangan</th><td>{{ $distribusi->keterangan ?: '-' }}</td></tr>
						<tr><th>Created By</th><td>{{ optional($distribusi->createdBy)->name ?? '-' }}</td></tr>
						<tr><th>Updated By</th><td>{{ optional($distribusi->updatedBy)->name ?? '-' }}</td></tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
@endsection
