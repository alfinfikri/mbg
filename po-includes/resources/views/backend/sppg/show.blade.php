@extends('layouts.app')
@section('title', 'Detail SPPG')

@php
    $statusLabels = [
        1 => ['success', 'Aktif'],
        2 => ['warning', 'Tidak Aktif'],
        3 => ['secondary', 'Belum Operasi'],
    ];
    $status = $statusLabels[$sppg->status_layanan] ?? ['secondary', 'Tidak Ada Data'];
    $wilayah = $sppg->wilayah
        ? ($sppg->wilayah->nama_wilayah . (optional($sppg->wilayah->parent)->nama_wilayah ? ' - ' . optional($sppg->wilayah->parent)->nama_wilayah : ''))
        : '-';
@endphp

@section('content')
	<div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-20">
		<div>
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb breadcrumb-style1 mg-b-10">
					<li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">{{ __('general.dashboard') }}</a></li>
					<li class="breadcrumb-item"><a href="#">MASTER</a></li>
					<li class="breadcrumb-item"><a href="{{ url('/dashboard/sppgs/table') }}">SPPG</a></li>
					<li class="breadcrumb-item active" aria-current="page">Detail SPPG</li>
				</ol>
			</nav>
			<h4 class="mg-b-0 tx-spacing--1">Detail SPPG</h4>
		</div>

		<div>
            <a href="{{ url('dashboard/sppgs/table') }}" class="btn btn-sm pd-x-15 btn-white btn-uppercase mg-t-10"><i data-feather="arrow-left" class="wd-10 mg-r-5"></i> {{ __('general.back') }}</a>
            <a href="{{ url('dashboard/sppgs/'.Hashids::encode($sppg->id).'/edit') }}" class="btn btn-sm pd-x-15 btn-primary btn-uppercase mg-t-10"><i data-feather="edit" class="wd-10 mg-r-5"></i> {{ __('general.edit') }}</a>
        </div>
	</div>

	<div class="card">
		<div class="card-body">
            <div class="d-flex justify-content-between align-items-start mg-b-20">
                <div>
                    <h4 class="mg-b-5">{{ $sppg->nama }}</h4>
                    <span class="badge badge-{{ $status[0] }}">{{ $status[1] }}</span>
                </div>
            </div>

			<div class="table-responsive">
				<table class="table table-striped">
					<tbody>
						<tr>
							<th style="width:220px;">Alamat</th>
                            <td>{{ $sppg->alamat ?: '-' }}</td>
						</tr>
						<tr>
							<th>Wilayah</th>
                            <td>{{ $wilayah }}</td>
						</tr>
						<tr>
							<th>Penanggung Jawab</th>
                            <td>{{ $sppg->nama_penanggung_jawab ?: '-' }}</td>
						</tr>
						<tr>
							<th>Kontak</th>
                            <td>
                                {{ $sppg->no_hp ?: '-' }}
                                @if($sppg->email)
                                    <br>{{ $sppg->email }}
                                @endif
                            </td>
						</tr>
						<tr>
							<th>Ahli Gizi</th>
                            <td>{{ $sppg->nama_ahli_gizi ?: '-' }}</td>
						</tr>
						<tr>
							<th>Keterangan Data Profil</th>
                            <td>{{ $sppg->keterangan_data_profil ?: '-' }}</td>
						</tr>
						<tr>
							<th>SLHS</th>
                            <td>
                                Nomor: {{ $sppg->slhs_nomor ?: '-' }}
                                <br>Tanggal Terbit: {{ $sppg->slhs_tanggal_terbit ?: $sppg->slhs_tanggal ?: '-' }}
                                <br>Berlaku Hingga: {{ $sppg->slhs_berlaku_hingga ?: '-' }}
                                @if($sppg->slhs_file)
                                    <br><a href="{{ asset('po-content/uploads/'.$sppg->slhs_file) }}" target="_blank">Lihat file SLHS</a>
                                @endif
                            </td>
						</tr>
						<tr>
							<th>Sertifikat Halal</th>
                            <td>
                                Nomor: {{ $sppg->halal_nomor ?: '-' }}
                                <br>Tanggal Terbit: {{ $sppg->halal_tanggal_terbit ?: '-' }}
                                @if($sppg->halal_file)
                                    <br><a href="{{ asset('po-content/uploads/'.$sppg->halal_file) }}" target="_blank">Lihat file sertifikat halal</a>
                                @endif
                            </td>
						</tr>
						<tr>
							<th>Foto Dapur</th>
                            <td>
                                @if($sppg->foto_dapur)
                                    <a href="{{ asset('po-content/uploads/'.$sppg->foto_dapur) }}" target="_blank">Lihat foto dapur</a>
                                @else
                                    -
                                @endif
                            </td>
						</tr>
						<tr>
							<th>Fasilitas Dapur</th>
                            <td>{{ $sppg->fasilitas_dapur ?: '-' }}</td>
						</tr>
						<tr>
							<th>Kapasitas Produksi</th>
                            <td>{{ number_format($sppg->kapasitas_produksi) }}</td>
						</tr>
						<tr>
							<th>Jumlah Petugas</th>
                            <td>{{ number_format($sppg->jumlah_petugas) }}</td>
						</tr>
						<tr>
							<th>Koordinat</th>
                            <td>{{ $sppg->latitude ?: '-' }}, {{ $sppg->longitude ?: '-' }}</td>
						</tr>
						<tr>
							<th>Total Penerima</th>
                            <td>{{ number_format($totalPenerima) }}</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>

    <div class="card mg-t-20">
        <div class="card-header">
            <h6 class="mg-b-0">Sekolah yang Dilayani</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th style="width:50px;">#</th>
                            <th>Nama Sekolah</th>
                            <th>Wilayah</th>
                            <th>Jumlah Penerima</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sppg->sekolahs as $index => $sekolah)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $sekolah->nama }}</td>
                                <td>
                                    @if($sekolah->wilayah)
                                        {{ $sekolah->wilayah->nama_wilayah }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ number_format($sekolah->jumlah_total) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">Belum ada sekolah yang dilayani</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
