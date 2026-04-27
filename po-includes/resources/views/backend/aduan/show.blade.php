@extends('layouts.app')
@section('title', __('Verifikasi Aduan'))

@section('content')
	<div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-20">
		<div>
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb breadcrumb-style1 mg-b-10">
					<li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ __('general.dashboard') }}</a></li>
					<li class="breadcrumb-item"><a href="{{ url('/dashboard/aduans/table') }}">MASTER</a></li>
					<li class="breadcrumb-item"><a href="{{ url('/dashboard/aduans/table') }}">ADUAN</a></li>
					<li class="breadcrumb-item active" aria-current="page">LIST ADUAN</li>
				</ol>
			</nav>
			<h4 class="mg-b-0 tx-spacing--1">Aduan MBG</h4>
		</div>
		
		<div><a href="{{ url('dashboard/aduans/table') }}" class="btn btn-sm pd-x-15 btn-white btn-uppercase mg-t-10"><i data-feather="arrow-left" class="wd-10 mg-r-5"></i> {{ __('general.back') }}</a></div>
	</div>
	
	<div class="card">
		<div class="card-body">

			<div class="d-flex">
				<!-- Avatar -->
				<div class="mg-r-10">
					<img src="{{ asset('po-content/uploads/user-1.jpg') }}" class="rounded-circle" width="45">
				</div>

				<!-- Konten -->
				<div style="width: 100%;">
					
					<!-- Info -->
					<ul class="list-inline mg-b-5">
						<li class="list-inline-item">Nama Pelapor : {{ $aduan->nama }}</li>
						<li class="list-inline-item">/</li>
						<li class="list-inline-item">{{ date('d F Y', strtotime($aduan->tgl_aduan)) }}</li>		
						<li class="list-inline-item">/</li>
						<li class="list-inline-item">No Telepon - WA : {{ $aduan->no_hp }}</li>
						<li class="list-inline-item">/</li>
						<li class="list-inline-item">Lokasi : {{ $aduan->alamat }}</li>	
						<li class="list-inline-item">/</li>
						<li class="list-inline-item">
							{{ $aduan->wilayah->parent->nama_wilayah ?? '-' }} - 
							{{ $aduan->wilayah->nama_wilayah }}
						</li>
					</ul>

					<!-- Judul -->
					<h5 class="mg-b-10">{{ $aduan->judul_aduan }}</h5>

					<!-- Isi -->
					<div class="mg-b-10">
						{!! $aduan->isi_aduan !!}
					</div>

					<!-- Foto -->
					@if($aduan->foto)
						<div class="mg-t-10">
							<img src="{{ asset('storage/aduan/' . $aduan->foto) }}" class="img-fluid rounded">
						</div>
					@endif

				</div>
			</div>

		</div>
	</div>
	
	@if($aduan->user_id)

		<div class="card-footer mg-t-20">
			<div class="alert alert-info d-flex align-items-center">
				
				<div>
					Aduan sudah didisposisikan ke :
					<strong>{{ $aduan->user->name ?? '-' }}</strong>
					<br>
					<small>
						Tanggal Disposisi: {{ date('d M Y', strtotime($aduan->tgl_proses)) }}
					</small>
				</div>
			</div>
		</div>

	@endif

	@if($aduan->status >= 1 && $aduan->respon_proses)
		<div class="card mg-t-10 mg-l-20 mg-b-20">
			<div class="card-body">
				<div class="d-flex">
					<!-- Avatar -->
					<div class="mg-r-10">
						<img src="{{ asset('po-content/uploads/user-1.jpg') }}" class="rounded-circle" width="45">
					</div>

					<!-- Konten -->
					<div>
						<h6 class="mg-b-5">
							Tanggapan / Respon Awal Satgas
							<small class="text-muted">
								{{ $aduan->updated_at->diffForHumans() }}
							</small>
						</h6>
						<!-- User -->
						<small class="text-muted">
							{{ $aduan->user->name ?? '-' }}
						</small>

						<!-- Isi respon -->
						<p class="mg-t-10 mg-b-5">
							{!! html_entity_decode($aduan->respon_proses ?? 'Belum ada tanggapan') !!}
						</p>

					</div>
				</div>
			</div>
		</div>
	@endif

	@if($aduan->status == 3 && $aduan->respon_selesai)

		<div class="card mg-t-10 mg-l-40">
			<div class="card-body">
				<div class="d-flex">
					
					<!-- Avatar -->
					<div class="mg-r-10">
						<img src="{{ asset('po-content/uploads/user-1.jpg') }}" class="rounded-circle" width="45">
					</div>

					<!-- Konten -->
					<div>
						<h6 class="mg-b-5">
							Respon Akhir Satgas
							<small class="text-muted">
								{{ $aduan->updated_at->diffForHumans() }}
							</small>
						</h6>

						<small class="text-muted">
							{{ $aduan->user->name ?? '-' }}
						</small>

						<p class="mg-t-10 mg-b-5">
							{!! html_entity_decode($aduan->respon_selesai) !!}
						</p>
					</div>

				</div>
			</div>
		</div>

		@endif

		@if($isAdmin && !$aduan->user_id)
		<div class="card-footer">
			<form action="{{ url('dashboard/aduans/' . $aduan->id . '/proses') }}" method="POST">
				@csrf
				@method('PUT')

				<div class="row">
					<!-- Tanggal Disposisi -->
					<input type="hidden" name="tgl_disposisi" class="form-control" value="{{ date('Y-m-d') }}" required>
				
					<!-- Pilih User -->
					<div class="col-md-4">
						<label>Disposisi Satgas</label>
						<select name="user_id" class="form-control" required>
							<option value="">-- Pilih User --</option>
							@foreach($users as $user)
								<option value="{{ $user->id }}">
									{{ $user->name }}
								</option>
							@endforeach
						</select>
					</div>

					<!-- Tombol -->
					<div class="col-md-4 d-flex align-items-end">
						<button type="submit" class="btn btn-primary"><i data-feather="send" class="wd-10 mg-r-5"></i> Disposisi</button>
					</div>
				</div>
			</form>
		</div>
		@endif
		
		@if(!$isAdmin && auth()->id() == $aduan->user_id && $aduan->status == 1)
		<div class="card-footer">
			<form action="{{ url('dashboard/aduans/' . $aduan->id . '/respon-awal') }}" method="POST">
				@csrf
				@method('PUT')
				<!-- Tanggal Proses -->
				<input type="hidden" name="tgl_proses" class="form-control" value="{{ date('Y-m-d') }}" required>
				
				{!! Form::label('respon', 'Respon Awal Satgas', ['class' => 'control-label']) !!}
				{!! Form::textarea('respon', null, ['class' => 'form-control ht-300-i editor']) !!}
				{!! $errors->first('respon', '<p class="text-danger">:message</p>') !!}

				<div class="mt-3">
					<button type="submit" class="btn btn-success">
						<i data-feather="send" class="wd-10 mg-r-5"></i> Kirim Respon Awal
					</button>
				</div>
			</form>
		</div>
		@endif

		@if(!$isAdmin && auth()->id() == $aduan->user_id && $aduan->status == 2)

		<div class="card-footer">
			<form action="{{ url('dashboard/aduans/' . $aduan->id . '/respon-akhir') }}" method="POST">
				@csrf
				@method('PUT')
				<!-- Tanggal Selesai -->
				<input type="hidden" name="tgl_selesai" class="form-control" value="{{ date('Y-m-d') }}" required>
				
				{!! Form::label('respon_selesai', 'Respon Akhir Satgas') !!}
				{!! Form::textarea('respon_selesai', null, ['class' => 'form-control ht-300-i editor']) !!}

				<div class="mt-3">
					<button type="submit" class="btn btn-success">
						<i data-feather="send" class="wd-10 mg-r-5"></i> Selesaikan Aduan
					</button>
				</div>
			</form>
		</div>

		@endif
	
	
@endsection
@push('scripts')
<script src="{{ asset('po-admin/lib/tinymce/tinymce.min.js') }}" referrerpolicy="origin"></script>
<script src="{{ asset('po-admin/lib/bootstrap-tagsinput/bootstrap-tagsinput.min.js') }}"></script>
<script src="{{ asset('po-admin/lib/typeahead.js/typeahead.bundle.min.js') }}"></script>

<script type="text/javascript">
	tinymce.init({
		selector: ".editor",
		height: 400,
		min_height: 400,
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
</script>
@endpush
