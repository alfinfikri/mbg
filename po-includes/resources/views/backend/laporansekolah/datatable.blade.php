@extends('layouts.app')
@section('title', 'Laporan Sekolah')

@php
	$showSekolahColumn = !Auth::user()->hasRole('sekolah')
		|| Auth::user()->hasRole('superadmin')
		|| Auth::user()->hasRole('superadmin 2')
		|| Auth::user()->hasRole('admin');
	$showBulkCheckbox = Auth::user()->hasRole('superadmin');
	$canDeleteSelected = Auth::user()->can('delete-laporan-sekolahs')
		&& $showBulkCheckbox;
	$showSppgColumn = !Auth::user()->hasRole('sppg');
	$deleteSelectedColspan = 8 + ($showSekolahColumn ? 1 : 0) + ($showSppgColumn ? 1 : 0);
@endphp

@section('content')
	<div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-20">
		<div>
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb breadcrumb-style1 mg-b-10">
					<li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ __('general.dashboard') }}</a></li>
					<li class="breadcrumb-item active" aria-current="page">Laporan Sekolah</li>
				</ol>
			</nav>
			<h4 class="mg-b-0 tx-spacing--1">Laporan Sekolah</h4>
		</div>
		@if(Auth::user()->can('create-laporan-sekolahs') && (Auth::user()->sekolah_id || Auth::user()->hasRole('superadmin') || Auth::user()->hasRole('superadmin 2') || Auth::user()->hasRole('admin')))
			<div><a href="{{ url('dashboard/laporan-sekolahs/create') }}" class="btn btn-sm pd-x-15 btn-white btn-uppercase mg-t-10"><i data-feather="plus" class="wd-10 mg-r-5"></i> {{ __('general.add') }}</a></div>
		@endif
	</div>

	<div class="card mg-b-20">
		<div class="card-body">
			<div class="form-row">
				<div class="form-group col-md-3">
					<label>Tanggal</label>
					<input type="date" id="filter_tanggal" class="form-control">
				</div>
				<div class="form-group col-md-3">
					<label>Status</label>
					<select id="filter_status_laporan" class="form-control">
						<option value="">Semua Status</option>
						<option value="1">Sudah Lapor</option>
						<option value="2">Terverifikasi</option>
						<option value="3">Perlu Perbaikan / Ditolak</option>
					</select>
				</div>
			</div>
			<button type="button" id="filter-button" class="btn btn-primary btn-sm"><i data-feather="filter" class="wd-10 mg-r-5"></i> Filter</button>
			<a href="{{ url('dashboard/laporan-sekolahs/table') }}" class="btn btn-white btn-sm">Reset</a>
		</div>
	</div>

	<div class="card">
		{!! Form::open(['url' => 'dashboard/laporan-sekolahs/deleteall', 'method' => 'post', 'class' => 'form-horizontal']) !!}
			<input type="hidden" name="totaldata" id="totaldata" value="0" />
			<div class="table-responsive">
				<table class="table table-striped" id="laporan-sekolahs-table">
					<thead>
						<tr>
							@if($showBulkCheckbox)
							<th></th>
							@endif
							<th>No</th>
							<th>Tanggal</th>
							@if($showSekolahColumn)
							<th>Sekolah</th>
							@endif
							@if($showSppgColumn)
							<th>SPPG</th>
							@endif
							<th>Menu Harian</th>
							<th>Rating</th>
							<th>Foto Menu</th>
							<th>Foto Siswa</th>
							<th>Status</th>
							<th>Action</th>
						</tr>
					</thead>
					@if($canDeleteSelected)
					<tfoot>
						<tr>
							<td style="width:10px;text-align:center;">
								<input type="checkbox" id="titleCheck" data-toggle="tooltip" title="{{ __('general.check_all') }}" />
							</td>
							<td colspan="{{ $deleteSelectedColspan }}">
								<button class="btn btn-sm btn-danger" type="button" data-toggle="modal" data-target="#alertalldel"><i class="fa fa-trash"></i> {{ __('general.delete_selected') }}</button>
							</td>
						</tr>
					</tfoot>
					@endif
				</table>
			</div>
		{!! Form::close() !!}
	</div>

	<form id="verify-form" method="post" style="display:none;">
		@csrf
		<input type="hidden" name="catatan_verifikasi" value="">
	</form>
	<form id="reject-form" method="post" style="display:none;">
		@csrf
		<input type="hidden" name="catatan_verifikasi" id="reject-note">
	</form>
@endsection

@push('scripts')
<script>
	$(function () {
		var table = $('#laporan-sekolahs-table').DataTable({
			processing: true,
			serverSide: true,
			responsive: true,
			ajax: {
				url: '{{ url("dashboard/laporan-sekolahs/data") }}',
				data: function (d) {
					d.tanggal = $('#filter_tanggal').val();
					d.status_laporan = $('#filter_status_laporan').val();
				}
			},
			columns: [
				@if($showBulkCheckbox)
				{ data: 'check', name: 'check', orderable: false, searchable: false },
				@endif
				{ data: 'id', name: 'laporan_sekolahs.id' },
				{ data: 'tanggal', name: 'laporan_sekolahs.tanggal' },
				@if($showSekolahColumn)
				{ data: 'sekolah', name: 'sekolah', orderable: false, searchable: false },
				@endif
				@if($showSppgColumn)
				{ data: 'sppg', name: 'sppg', orderable: false, searchable: false },
				@endif
				{ data: 'menu_harian', name: 'menu_harian', orderable: false, searchable: false },
				{ data: 'rating', name: 'laporan_sekolahs.rating' },
				{ data: 'foto_menu', name: 'laporan_sekolahs.foto_menu', orderable: false, searchable: false },
				{ data: 'foto_siswa', name: 'laporan_sekolahs.foto_siswa', orderable: false, searchable: false },
				{ data: 'status_laporan', name: 'laporan_sekolahs.status_laporan' },
				{ data: 'action', name: 'action', orderable: false, searchable: false }
			],
			order: [[{{ $showBulkCheckbox ? 1 : 0 }}, 'desc']],
			drawCallback: function () {
				$('#titleCheck').off('click').on('click', function () {
					var checkedStatus = this.checked;
					$('#laporan-sekolahs-table tbody tr td div:first-child input[type=checkbox]').each(function () {
						this.checked = checkedStatus;
						$(this).closest('tr').toggleClass('selected', this.checked);
						$(this).closest('tr').find('input:hidden').attr('disabled', !this.checked);
					});
					$('#totaldata').val($('#laporan-sekolahs-table tbody input[type=checkbox]:checked').length);
				});

				$('#laporan-sekolahs-table tbody tr td div:first-child input[type=checkbox]').off('click change').on('click change', function () {
					$(this).closest('tr').toggleClass('selected', this.checked);
					$(this).closest('tr').find('input:hidden').attr('disabled', !this.checked);
					$('#totaldata').val($('#laporan-sekolahs-table tbody input[type=checkbox]:checked').length);
				});

				deleter.init();
				$('[data-toggle="tooltip"]').tooltip();
			}
		});

		$('#filter-button').on('click', function () {
			table.ajax.reload();
		});

		$(document).on('click', '[data-verify]', function (e) {
			e.preventDefault();
			if (confirm('Verifikasi laporan sekolah ini?')) {
				$('#verify-form').attr('action', $(this).attr('href') + '/verify').submit();
			}
		});

		$(document).on('click', '[data-reject]', function (e) {
			e.preventDefault();
			var note = prompt('Catatan perbaikan wajib diisi');
			if (note) {
				$('#reject-note').val(note);
				$('#reject-form').attr('action', $(this).attr('href') + '/reject').submit();
			}
		});
	});
</script>
@endpush
