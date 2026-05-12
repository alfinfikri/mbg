@extends('layouts.app')
@section('title', 'Distribusi MBG')

@section('content')
	@php
		$showSppgColumn = !Auth::user()->hasRole('sppg')
			&& (Auth::user()->hasRole('superadmin') || Auth::user()->hasRole('superadmin 2') || Auth::user()->hasRole('admin'));
		$showBulkCheckbox = Auth::user()->hasRole('superadmin');
		$canDeleteSelected = Auth::user()->can('delete-distribusis') && $showBulkCheckbox;
		$deleteSelectedColspan = $showSppgColumn ? 8 : 7;
	@endphp
	<div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-20">
		<div>
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb breadcrumb-style1 mg-b-10">
					<li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ __('general.dashboard') }}</a></li>
					<li class="breadcrumb-item active" aria-current="page">Distribusi MBG</li>
				</ol>
			</nav>
			<h4 class="mg-b-0 tx-spacing--1">Distribusi MBG</h4>
		</div>
	</div>

	<div class="row row-xs mg-b-20">
		<div class="col-md">
			<div class="card"><div class="card-body"><span class="tx-12 tx-color-03">Total Sekolah Tujuan</span><h3 class="mg-b-0">{{ $summary['total'] }}</h3></div></div>
		</div>
		<div class="col-md">
			<div class="card"><div class="card-body"><span class="tx-12 tx-color-03">Belum Lapor</span><h3 class="mg-b-0 tx-warning">{{ $summary['belum_lapor'] }}</h3></div></div>
		</div>
		<div class="col-md">
			<div class="card"><div class="card-body"><span class="tx-12 tx-color-03">Sudah Lapor Sekolah</span><h3 class="mg-b-0 tx-info">{{ $summary['sudah_lapor'] }}</h3></div></div>
		</div>
	</div>

	<div class="card mg-b-20">
		<div class="card-body">
			<div class="form-row">
				<div class="form-group col-md-4">
					<label>Tanggal</label>
					<input type="date" id="filter_tanggal" class="form-control" value="{{ request('tanggal') }}">
				</div>
				<div class="form-group col-md-4">
					<label>SPPG</label>
					<select id="filter_sppg_id" class="form-control">
						<option value="">Semua SPPG</option>
						@foreach($sppgs as $id => $nama)
							<option value="{{ $id }}" {{ request('sppg_id') == $id ? 'selected' : '' }}>{{ $nama }}</option>
						@endforeach
					</select>
				</div>
				<div class="form-group col-md-4">
					<label>Sekolah</label>
					<select id="filter_sekolah_id" class="form-control">
						<option value="">Semua Sekolah</option>
						@foreach($sekolahs as $id => $nama)
							<option value="{{ $id }}" {{ request('sekolah_id') == $id ? 'selected' : '' }}>{{ $nama }}</option>
						@endforeach
					</select>
				</div>
			</div>
			<button type="button" id="filter-button" class="btn btn-primary btn-sm"><i data-feather="filter" class="wd-10 mg-r-5"></i> Filter</button>
			<a href="{{ url('dashboard/distribusis/table') }}" class="btn btn-white btn-sm">Reset</a>
		</div>
	</div>

	<div class="card">
		{!! Form::open(['url' => 'dashboard/distribusis/deleteall', 'method' => 'post', 'class' => 'form-horizontal']) !!}
			<input type="hidden" name="totaldata" id="totaldata" value="0" />
			<div class="table-responsive">
				<table class="table table-striped" id="distribusis-table">
					<thead>
						<tr>
							@if($showBulkCheckbox)
							<th></th>
							@endif
							<th>No</th>
							<th>Tanggal</th>
							@if($showSppgColumn)
								<th>SPPG</th>
							@endif
							<th>Sekolah</th>
							<th>Menu Harian</th>
							<th>Jumlah Porsi</th>
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
@endsection

@push('scripts')
<script>
	$(function () {
		var table = $('#distribusis-table').DataTable({
			processing: true,
			serverSide: true,
			responsive: true,
			ajax: {
				url: '{{ url("dashboard/distribusis/data") }}',
				data: function (d) {
					d.tanggal = $('#filter_tanggal').val();
					d.sppg_id = $('#filter_sppg_id').val();
					d.sekolah_id = $('#filter_sekolah_id').val();
				}
			},
			columns: [
				@if($showBulkCheckbox)
				{ data: 'check', name: 'check', orderable: false, searchable: false },
				@endif
				{ data: 'id', name: 'distribusis.id' },
				{ data: 'tanggal', name: 'distribusis.tanggal' },
				@if($showSppgColumn)
					{ data: 'sppg', name: 'sppg', orderable: false, searchable: false },
				@endif
				{ data: 'sekolah', name: 'sekolah', orderable: false, searchable: false },
				{ data: 'menu_harian', name: 'menu_harian', orderable: false, searchable: false },
				{ data: 'jumlah_porsi', name: 'distribusis.jumlah_porsi' },
				{ data: 'status_distribusi', name: 'distribusis.status_distribusi' },
				{ data: 'action', name: 'action', orderable: false, searchable: false }
			],
			order: [[{{ $showBulkCheckbox ? 1 : 0 }}, 'desc']],
			drawCallback: function () {
				$('#titleCheck').off('click').on('click', function () {
					var checkedStatus = this.checked;
					$('#distribusis-table tbody tr td div:first-child input[type=checkbox]').each(function () {
						this.checked = checkedStatus;
						$(this).closest('tr').toggleClass('selected', this.checked);
						$(this).closest('tr').find('input:hidden').attr('disabled', !this.checked);
					});
					$('#totaldata').val($('#distribusis-table tbody input[type=checkbox]:checked').length);
				});

				$('#distribusis-table tbody tr td div:first-child input[type=checkbox]').off('click change').on('click change', function () {
					$(this).closest('tr').toggleClass('selected', this.checked);
					$(this).closest('tr').find('input:hidden').attr('disabled', !this.checked);
					$('#totaldata').val($('#distribusis-table tbody input[type=checkbox]:checked').length);
				});

				deleter.init();
				$('[data-toggle="tooltip"]').tooltip();
			}
		});

		$('#filter-button').on('click', function () {
			table.ajax.reload();
		});
	});
</script>
@endpush
