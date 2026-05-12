@php
    $distribusiSekolahs = $distribusiSekolahs ?? collect();
    $distribusis = $distribusis ?? collect();
    $selectedSekolahIds = collect(old('distribusi_sekolah_ids', $distribusis->keys()->all()))
        ->map(fn($id) => (int) $id)
        ->all();
@endphp

<div class="card mg-b-20">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h6 class="mg-b-0">Distribusi ke Sekolah</h6>
        <span class="badge badge-light">{{ $distribusiSekolahs->count() }} sekolah tersedia</span>
    </div>
    <div class="card-body">
        @if($distribusiSekolahs->isEmpty())
            <div class="alert alert-warning mg-b-0">
                Belum ada sekolah yang bisa dipilih untuk SPPG ini.
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-striped table-sm mg-b-0">
                    <thead>
                        <tr>
                            <th style="width:50px;">Pilih</th>
                            <th>Sekolah</th>
                            <th style="width:160px;">Default Penerima</th>
                            <th style="width:170px;">Jumlah Porsi</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($distribusiSekolahs as $sekolah)
                            @php
                                $distribusi = $distribusis->get($sekolah->id);
                                $checked = in_array((int) $sekolah->id, $selectedSekolahIds, true);
                                $porsi = old('distribusi_porsi.'.$sekolah->id, $distribusi->jumlah_porsi ?? $sekolah->jumlah_total);
                                $keterangan = old('distribusi_keterangan.'.$sekolah->id, $distribusi->keterangan ?? '');
                            @endphp
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="distribusi_sekolah_ids[]" value="{{ $sekolah->id }}" {{ $checked ? 'checked' : '' }}>
                                </td>
                                <td>
                                    <strong>{{ $sekolah->nama }}</strong>
                                    @if($distribusi && (int) $distribusi->status_distribusi > 1)
                                        <span class="badge badge-info mg-l-5">Sudah Lapor Sekolah</span>
                                    @endif
                                </td>
                                <td>{{ number_format($sekolah->jumlah_total, 0, ',', '.') }}</td>
                                <td>
                                    <input type="number" name="distribusi_porsi[{{ $sekolah->id }}]" value="{{ $porsi }}" min="0" class="form-control form-control-sm">
                                </td>
                                <td>
                                    <input type="text" name="distribusi_keterangan[{{ $sekolah->id }}]" value="{{ $keterangan }}" class="form-control form-control-sm" placeholder="Opsional">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <small class="d-block tx-color-03 mg-t-10">
                Sekolah yang tidak dicentang akan dilepas dari rencana distribusi jika belum dikonfirmasi sekolah.
            </small>
        @endif
    </div>
</div>
