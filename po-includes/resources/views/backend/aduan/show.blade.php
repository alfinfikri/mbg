@extends('layouts.app')
@section('title', 'Detail Aduan MBG')

@push('styles')
<style>
	.aduan-shell {
		display: grid;
		grid-template-columns: minmax(280px, 380px) minmax(0, 1fr);
		gap: 20px;
		align-items: start;
	}

	.aduan-shell.is-readonly {
		grid-template-columns: 1fr;
	}

	.aduan-sidebar {
		position: sticky;
		top: 82px;
	}

	.aduan-card {
		border: 1px solid #e5e9f2;
		border-radius: 8px;
		box-shadow: 0 2px 8px rgba(28, 39, 60, .04);
	}

	.aduan-card .card-header {
		background-color: #fff;
		border-bottom-color: #edf1f7;
		padding: 15px 18px;
	}

	.aduan-card .card-header.d-flex {
		gap: 12px;
	}

	.aduan-card .card-header .badge {
		flex-shrink: 0;
		text-align: left;
		white-space: normal;
	}

	.aduan-card .card-body {
		padding: 18px;
	}

	.aduan-summary {
		display: grid;
		grid-template-columns: repeat(2, minmax(0, 1fr));
		gap: 14px 18px;
	}

	.aduan-field {
		min-width: 0;
	}

	.aduan-field-label {
		display: block;
		color: #8392a5;
		font-size: 11px;
		font-weight: 700;
		letter-spacing: .4px;
		text-transform: uppercase;
		margin-bottom: 3px;
	}

	.aduan-field-value {
		color: #1b2e4b;
		font-size: 14px;
		line-height: 1.45;
		overflow-wrap: anywhere;
	}

	.aduan-body-text {
		max-height: 280px;
		overflow: auto;
		padding: 12px 14px;
		background: #f8fafc;
		border: 1px solid #edf1f7;
		border-radius: 6px;
		line-height: 1.6;
	}

	.aduan-photo {
		max-height: 320px;
		width: auto;
		object-fit: contain;
		background: #f8fafc;
	}

	.aduan-response-item {
		padding: 14px 0;
		border-bottom: 1px solid #edf1f7;
	}

	.aduan-response-item:last-child {
		padding-bottom: 0;
		border-bottom: 0;
	}

	.aduan-action-stack .card,
	.aduan-action-stack form {
		margin-bottom: 15px;
	}

	.aduan-action-stack .btn-block {
		white-space: normal;
	}

	@media (max-width: 991.98px) {
		.aduan-shell {
			grid-template-columns: 1fr;
		}

		.aduan-sidebar {
			position: static;
		}
	}

	@media (max-width: 575.98px) {
		.aduan-summary {
			grid-template-columns: 1fr;
		}

		.aduan-card .card-header.d-flex {
			flex-direction: column;
		}

		.aduan-card .card-header,
		.aduan-card .card-body {
			padding: 14px;
		}

		.aduan-body-text {
			max-height: none;
		}
	}
</style>
@endpush

@section('content')
@php
	$status = (int) ($aduan->status_pengaduan ?? $aduan->status);
	$statusMap = [
		0 => ['Aduan diterima', 'secondary'],
		1 => ['Aduan sudah didisposisikan', 'info'],
		2 => ['Aduan sedang diproses', 'warning'],
		3 => ['Aduan selesai', 'success'],
		4 => ['Aduan ditolak', 'danger'],
	];
	$statusInfo = $statusMap[$status] ?? ['-', 'secondary'];
	$actionId = request()->route('id') ?: $aduan->id;
	$assignedSatgasUsers = $aduan->disposisiSatgasUsers();
	$currentSatgasResponse = $satgasResponses[(string) Auth::id()]['tanggapan'] ?? '';
	$hasActions = $canProcess || $canDisposisi || $canFollowup || $canClose || $canReject;
	$sppgName = optional($aduan->disposisiSppg)->nama ?? optional($aduan->sppg)->nama ?? '-';
@endphp

<div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-20">
	<div>
		<nav aria-label="breadcrumb">
			<ol class="breadcrumb breadcrumb-style1 mg-b-10">
				<li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ __('general.dashboard') }}</a></li>
				<li class="breadcrumb-item"><a href="{{ url('/dashboard/aduans/table') }}">ADUAN</a></li>
				<li class="breadcrumb-item active" aria-current="page">DETAIL</li>
			</ol>
		</nav>
		<h4 class="mg-b-0 tx-spacing--1">Aduan MBG</h4>
	</div>

	<div><a href="{{ url('dashboard/aduans/table') }}" class="btn btn-sm pd-x-15 btn-white btn-uppercase mg-t-10"><i data-feather="arrow-left" class="wd-10 mg-r-5"></i> {{ __('general.back') }}</a></div>
</div>

@if(session('flash_message'))
	<div class="alert alert-success">{{ session('flash_message') }}</div>
@endif
@if($errors->any())
	<div class="alert alert-danger">
		@foreach($errors->all() as $error)
			<div>{{ $error }}</div>
		@endforeach
	</div>
@endif

<div class="aduan-shell {{ $hasActions ? '' : 'is-readonly' }}">
	@if($hasActions)
	<div class="aduan-sidebar">
		<div class="aduan-action-stack">
		@if($canProcess)
			<div class="card aduan-card">
				<div class="card-header d-flex justify-content-between align-items-start">
					<div>
						<h6 class="mg-b-2">Respon Aduan</h6>
						<small class="text-muted">Tanggapan resmi satgas.</small>
					</div>
					<span class="badge badge-{{ $statusInfo[1] }}">{{ $statusInfo[0] }}</span>
				</div>
				<div class="card-body">
					<form action="{{ url('dashboard/aduans/'.$actionId.'/proses') }}" method="POST">
						@csrf
						<div class="form-group">
							<label>Tanggapan Resmi Kepada Pelapor</label>
							<textarea name="tanggapan" id="tanggapan" rows="7" class="form-control tinymce-editor">{{ old('tanggapan', $currentSatgasResponse) }}</textarea>
						</div>
						<div class="form-group">
							<label>SPPG Tujuan</label>
							<select name="disposisi_sppg_id" id="disposisi_sppg_id" class="form-control">
								<option value="">-- Tidak diteruskan ke SPPG --</option>
								@foreach($sppgs as $id => $nama)
									<option value="{{ $id }}" {{ old('disposisi_sppg_id', $aduan->disposisi_sppg_id) == $id ? 'selected' : '' }}>{{ $nama }}</option>
								@endforeach
							</select>
							<small class="text-muted">Opsional jika perlu tindak lanjut teknis.</small>
						</div>
						<div class="form-group">
							<label>Catatan Teknis untuk SPPG</label>
							<textarea name="catatan_teknis" rows="3" class="form-control">{{ old('catatan_teknis') }}</textarea>
						</div>
						<button type="submit" class="btn btn-warning btn-block">Simpan Tanggapan</button>
					</form>
				</div>
			</div>
		@endif

		@if($canDisposisi)
			<div class="card aduan-card">
				<div class="card-header"><h6 class="mg-b-0">Disposisi Aduan</h6></div>
				<div class="card-body">
					<form action="{{ url('dashboard/aduans/'.$actionId.'/disposisi') }}" method="POST">
						@csrf
						<div class="form-group">
							<label>Satgas Aduan</label>
							<select name="disposisi_satgas_ids[]" id="disposisi_satgas_ids" class="form-control" multiple required>
								@foreach($satgasUsers as $user)
									<option value="{{ $user->id }}" {{ in_array($user->id, $selectedSatgasIds) ? 'selected' : '' }}>{{ $user->name }}</option>
								@endforeach
							</select>
							<small class="text-muted">Pilih 1 sampai 2 satgas.</small>
						</div>
						<div class="form-group">
							<label>Catatan Disposisi</label>
							<textarea name="catatan_disposisi" rows="3" class="form-control">{{ old('catatan_disposisi', $aduan->catatan_disposisi) }}</textarea>
						</div>
						<button type="submit" class="btn btn-primary btn-block">Simpan Disposisi</button>
					</form>
				</div>
			</div>
		@endif

		@if($canFollowup)
			<div class="card aduan-card">
				<div class="card-header">
					<h6 class="mg-b-1">Tindak Lanjut SPPG</h6>
					<small class="text-muted">{{ $sppgName }}</small>
				</div>
				<div class="card-body">
					<form action="{{ url('dashboard/aduans/'.$actionId.'/tindak-lanjut') }}" method="POST" enctype="multipart/form-data">
						@csrf
						<div class="form-group">
							<label>Tanggapan Teknis</label>
							<textarea name="tanggapan_sppg" id="tanggapan_sppg" rows="5" class="form-control tinymce-editor">{{ old('tanggapan_sppg', $aduan->tanggapan_sppg) }}</textarea>
						</div>
						<div class="form-group">
							<label>Foto Bukti Tindak Lanjut</label>
							<input type="file" name="foto_tindak_lanjut" class="form-control" accept="image/*">
						</div>
						<button type="submit" class="btn btn-info btn-block">Simpan Tindak Lanjut</button>
					</form>
				</div>
			</div>
		@endif

		@if($canClose)
			<form action="{{ url('dashboard/aduans/'.$actionId.'/close') }}" method="POST">
				@csrf
				<button type="submit" class="btn btn-success btn-block" onclick="return confirm('Selesaikan aduan ini?')">Selesaikan Aduan</button>
			</form>
		@endif

		@if($canReject)
			<div class="card aduan-card">
				<div class="card-header"><h6 class="mg-b-0">Tolak Aduan</h6></div>
				<div class="card-body">
					<form action="{{ url('dashboard/aduans/'.$actionId.'/reject') }}" method="POST">
						@csrf
						<div class="form-group">
							<label>Alasan Penolakan</label>
							<textarea name="tanggapan" id="tanggapan_penolakan" rows="3" class="form-control">{{ old('tanggapan') }}</textarea>
						</div>
						<button type="submit" class="btn btn-danger btn-block" onclick="return confirm('Tolak aduan ini?')">Tolak Aduan</button>
					</form>
				</div>
			</div>
		@endif
		</div>
	</div>
	@endif

	<div class="aduan-content">
		<div class="card aduan-card mg-b-20">
			<div class="card-header d-flex justify-content-between align-items-start">
				<div>
					<h6 class="mg-b-2">Ringkasan Aduan</h6>
					<small class="text-muted">Informasi utama pengaduan.</small>
				</div>
				<span class="badge badge-{{ $statusInfo[1] }}">{{ $statusInfo[0] }}</span>
			</div>
			<div class="card-body">
				<div class="aduan-summary">
					<div class="aduan-field">
						<span class="aduan-field-label">Kode Tiket</span>
						<div class="aduan-field-value tx-medium">{{ $aduan->kode_tiket }}</div>
					</div>
					<div class="aduan-field">
						<span class="aduan-field-label">Tanggal</span>
						<div class="aduan-field-value">{{ $aduan->tgl_aduan ? date('d F Y H:i', strtotime($aduan->tgl_aduan)) : '-' }}</div>
					</div>
					<div class="aduan-field">
						<span class="aduan-field-label">Pelapor</span>
						<div class="aduan-field-value">{{ $aduan->nama }}</div>
					</div>
					<div class="aduan-field">
						<span class="aduan-field-label">No HP</span>
						<div class="aduan-field-value">{{ $aduan->no_hp ?: '-' }}</div>
					</div>
					<div class="aduan-field">
						<span class="aduan-field-label">Wilayah</span>
						<div class="aduan-field-value">{{ $aduan->wilayah->nama_wilayah ?? '-' }}</div>
					</div>
					<div class="aduan-field">
						<span class="aduan-field-label">Alamat</span>
						<div class="aduan-field-value">{{ $aduan->alamat ?: '-' }}</div>
					</div>
					<div class="aduan-field" style="grid-column: 1 / -1;">
						<span class="aduan-field-label">Judul Aduan</span>
						<div class="aduan-field-value tx-medium">{{ $aduan->judul_aduan }}</div>
					</div>
				</div>

				<div class="mg-t-20">
					<span class="aduan-field-label">Isi Aduan</span>
					<div class="aduan-body-text">{!! nl2br(e($aduan->isi_aduan)) !!}</div>
				</div>

				@if($aduan->foto)
					<div class="mg-t-20">
						<span class="aduan-field-label">Foto Bukti</span>
						<a href="{{ asset('po-content/uploads/'.$aduan->foto) }}" target="_blank" data-fancybox="aduan-gallery">
							<img src="{{ asset('po-content/uploads/'.$aduan->foto) }}" class="img-fluid rounded aduan-photo" alt="Foto bukti aduan">
						</a>
					</div>
				@endif
			</div>
		</div>

		<div class="card aduan-card mg-b-20">
			<div class="card-header"><h6 class="mg-b-0">Data Disposisi</h6></div>
			<div class="card-body">
				<div class="aduan-summary">
					<div class="aduan-field">
						<span class="aduan-field-label">Satgas Tujuan</span>
						<div class="aduan-field-value">
							@if($assignedSatgasUsers->count())
								@foreach($assignedSatgasUsers as $satgas)
									<span class="badge badge-info mg-r-5 mg-b-5">{{ $satgas->name }}</span>
								@endforeach
							@else
								-
							@endif
						</div>
					</div>
					<div class="aduan-field">
						<span class="aduan-field-label">SPPG Tujuan</span>
						<div class="aduan-field-value">{{ $aduan->disposisiSppg->nama ?? '-' }}</div>
					</div>
					<div class="aduan-field">
						<span class="aduan-field-label">Waktu Disposisi</span>
						<div class="aduan-field-value">{{ $aduan->disposisi_at ? date('d F Y H:i', strtotime($aduan->disposisi_at)) : '-' }}</div>
					</div>
					<div class="aduan-field" style="grid-column: 1 / -1;">
						<span class="aduan-field-label">Catatan Disposisi</span>
						<div class="aduan-field-value">{!! nl2br(e($aduan->catatan_disposisi ?? '-')) !!}</div>
					</div>
				</div>
			</div>
		</div>

		<div class="card aduan-card mg-b-20">
			<div class="card-header"><h6 class="mg-b-0">Respons Satgas</h6></div>
			<div class="card-body">
				@if(!empty($satgasResponses))
					@foreach($satgasResponses as $response)
						<div class="aduan-response-item">
							<strong>{{ $response['name'] ?? 'Satgas' }}</strong>
							@if(!empty($response['responded_at']))
								<small class="text-muted">- {{ date('d F Y H:i', strtotime($response['responded_at'])) }}</small>
							@endif
							<div>{!! $response['tanggapan'] ?? '-' !!}</div>
						</div>
					@endforeach
				@elseif($aduan->tanggapan)
					{!! $aduan->tanggapan !!}
				@else
					<span class="text-muted">Belum ada tanggapan resmi.</span>
				@endif
			</div>
		</div>

		<div class="card aduan-card mg-b-20">
			<div class="card-header"><h6 class="mg-b-0">Tindak Lanjut SPPG</h6></div>
			<div class="card-body">
				<div class="mg-b-15">
					<span class="aduan-field-label">Nama SPPG</span>
					<div class="aduan-field-value tx-medium">{{ $sppgName }}</div>
				</div>
				<div>{!! $aduan->tanggapan_sppg ?: '<span class="text-muted">Belum ada tindak lanjut SPPG.</span>' !!}</div>
				@if($aduan->ditindaklanjuti_at)
					<p><strong>Waktu Tindak Lanjut:</strong> {{ date('d F Y H:i', strtotime($aduan->ditindaklanjuti_at)) }}</p>
				@endif
				@if($aduan->foto_tindak_lanjut)
					<a href="{{ asset('po-content/uploads/'.$aduan->foto_tindak_lanjut) }}" target="_blank" data-fancybox="aduan-gallery">
						<img src="{{ asset('po-content/uploads/'.$aduan->foto_tindak_lanjut) }}" class="img-fluid rounded aduan-photo" alt="Foto tindak lanjut">
					</a>
				@endif
			</div>
		</div>

		<div class="card aduan-card mg-b-20">
			<div class="card-header"><h6 class="mg-b-0">Data Final</h6></div>
			<div class="card-body">
				<div class="aduan-summary">
					<div class="aduan-field">
						<span class="aduan-field-label">Ditutup Oleh</span>
						<div class="aduan-field-value">{{ $aduan->closedBy->name ?? '-' }}</div>
					</div>
					<div class="aduan-field">
						<span class="aduan-field-label">Waktu Selesai</span>
						<div class="aduan-field-value">{{ $aduan->closed_at ? date('d F Y H:i', strtotime($aduan->closed_at)) : '-' }}</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@push('scripts')
@if($canProcess || $canFollowup || $canReject)
<script src="{{ asset('po-admin/lib/tinymce/tinymce.min.js') }}" referrerpolicy="origin"></script>
@endif
<script>
	$(function () {
		if ($.fn.select2) {
			$('#disposisi_satgas_ids').select2({
				placeholder: 'Pilih Satgas',
				allowClear: true,
				width: '100%',
				maximumSelectionLength: 2,
				dropdownParent: $('#disposisi_satgas_ids').closest('.form-group')
			});

			$('#disposisi_sppg_id').select2({
				placeholder: '-- Tidak diteruskan ke SPPG --',
				allowClear: true,
				width: '100%',
				dropdownParent: $('#disposisi_sppg_id').closest('.form-group')
			});
		}

		if (typeof tinymce !== 'undefined') {
			tinymce.init({
				selector: ".tinymce-editor",
				height: 300,
				min_height: 220,
				plugins: [
					"advlist autolink link image lists charmap print preview hr anchor pagebreak",
					"searchreplace wordcount visualblocks visualchars insertdatetime media nonbreaking",
					"table directionality emoticons paste",
					"save code fullscreen autoresize codesample autosave responsivefilemanager"
				],
				menubar : false,
				toolbar1: "undo redo restoredraft | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist hr | outdent indent table searchreplace",
				toolbar2: "| fontsizeselect | styleselect | responsivefilemanager emoticons link unlink anchor | image media emoticons | forecolor backcolor | code codesample fullscreen ",
				contextmenu: "link paste image imagetools table spellchecker",
				image_advtab: true,
				fontsize_formats: "8px 10px 12px 14px 18px 24px 36px",
				relative_urls: false,
				remove_script_host: false,
				external_filemanager_path: "{{ url('po-content/cms-pemkot/file') }}/",
				filemanager_title: "{{ __('general.filemanager') }}",
				external_plugins: {
					"filemanager" : "{{ asset('po-content/cms-pemkot/file/plugin.min.js') }}"
				},
				filemanager_access_key: '{{ config("fm.key") }}',
			});

			$('form').on('submit', function () {
				tinymce.triggerSave();
			});
		}
	});
</script>
@endpush
