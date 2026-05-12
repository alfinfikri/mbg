<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\LaporanSekolah;
use App\MenuHarian;
use App\Sekolah;
use App\Sppg;
use Artesaos\SEOTools\Facades\SEOTools;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Vinkla\Hashids\Facades\Hashids;

class LaporanHarianController extends Controller
{
	public function index(Request $request)
	{
		SEOTools::setTitle('Detail Laporan Harian - '.getSetting('web_name'));
		SEOTools::setDescription('Monitoring laporan harian SPPG dan sekolah - '.getSetting('web_description'));

		$viewData = $this->reportData($request);

		return view('frontend.laporan-harian.index', $viewData);
	}

	public function data(Request $request)
	{
		$viewData = $this->reportData($request);

		return response()->json([
			'html' => view('frontend.laporan-harian.partials.table', $viewData)->render(),
			'tanggalLabel' => $viewData['tanggalLabel'],
			'totalTarget' => $viewData['totalTarget'],
			'totalSudahLapor' => $viewData['totalSudahLapor'],
			'totalBelumLapor' => $viewData['totalBelumLapor'],
			'rowsTotal' => $viewData['rows']->total(),
		]);
	}

	private function reportData(Request $request)
	{
		$tanggal = $this->parseTanggal($request->input('tanggal'));
		$jenis = in_array($request->input('jenis'), ['sekolah', 'sppg']) ? $request->input('jenis') : 'sekolah';
		$status = in_array($request->input('status'), ['sudah', 'belum', 'rekap']) ? $request->input('status') : 'sudah';
		$search = trim((string) $request->input('search', ''));

		$data = $jenis === 'sppg'
			? $this->buildSppgRows($tanggal, $status, $search)
			: $this->buildSekolahRows($tanggal, $status, $search);

		return [
			'tanggal' => $tanggal->toDateString(),
			'tanggalLabel' => $tanggal->translatedFormat('l, d F Y'),
			'jenis' => $jenis,
			'status' => $status,
			'search' => $search,
			'totalTarget' => $data['totalTarget'],
			'totalSudahLapor' => $data['totalSudahLapor'],
			'totalBelumLapor' => $data['totalBelumLapor'],
			'rows' => $this->paginateCollection($data['rows'], $request),
		];
	}

	private function buildSekolahRows(Carbon $tanggal, $status, $search)
	{
		if (!class_exists(Sekolah::class) || !Schema::hasTable('sekolahs')) {
			return $this->emptyData();
		}

		$targetSekolahIds = $this->targetSekolahIds();
		$sekolahs = Sekolah::with(['wilayah.parent', 'sppgs.wilayah.parent'])
			->whereIn('id', $targetSekolahIds)
			->orderBy('nama')
			->get();

		$laporans = $this->laporanSekolahBySekolah($tanggal);
		$totalSudahLapor = $sekolahs->filter(function ($sekolah) use ($laporans) {
			return $laporans->has($sekolah->id);
		})->count();

		$rows = $sekolahs->map(function ($sekolah) use ($laporans) {
			$laporan = $laporans->get($sekolah->id);
			$sppg = $laporan && $laporan->sppg ? $laporan->sppg : $sekolah->sppgs->first();
			$statusLaporan = $laporan ? 'Sudah Lapor' : 'Belum Lapor';

			return [
				'nama' => $sekolah->nama,
				'sppg' => optional($sppg)->nama ?: '-',
				'jenjang' => $this->jenisSekolahLabel($sekolah->jenis_id),
				'lokasi' => $this->lokasi($sekolah),
				'foto_menu' => $laporan ? $this->uploadUrl($laporan->foto_menu) : null,
				'foto_siswa' => $laporan ? $this->uploadUrl($laporan->foto_siswa) : null,
				'waktu_update' => $laporan ? $this->formatWaktu($laporan->waktu_upload ?: $laporan->updated_at) : '-',
				'status_laporan' => $statusLaporan,
				'detail_url' => url('/sekolah/detail/'.Hashids::encode($sekolah->id)),
				'search_text' => implode(' ', [
					$sekolah->nama,
					optional($sppg)->nama,
					$this->jenisSekolahLabel($sekolah->jenis_id),
					$this->lokasi($sekolah),
				]),
			];
		});

		$rows = $this->filterRows($rows, $status, $search);

		return [
			'totalTarget' => $sekolahs->count(),
			'totalSudahLapor' => $totalSudahLapor,
			'totalBelumLapor' => max($sekolahs->count() - $totalSudahLapor, 0),
			'rows' => $rows,
		];
	}

	private function buildSppgRows(Carbon $tanggal, $status, $search)
	{
		if (!class_exists(Sppg::class) || !Schema::hasTable('sppgs')) {
			return $this->emptyData();
		}

		$sppgs = Sppg::with(['wilayah.parent'])
			->withCount('sekolahs')
			->where('status_layanan', 1)
			->orderBy('nama')
			->get();

		$menus = $this->menuHarianBySppg($tanggal);
		$totalSudahLapor = $sppgs->filter(function ($sppg) use ($menus) {
			return $menus->has($sppg->id);
		})->count();

		$rows = $sppgs->map(function ($sppg) use ($menus) {
			$menu = $menus->get($sppg->id);
			$statusLaporan = $menu ? 'Sudah Lapor' : 'Belum Lapor';

			return [
				'nama' => $sppg->nama,
				'kode' => '-',
				'lokasi' => $this->lokasi($sppg),
				'jumlah_sekolah' => $sppg->sekolahs_count,
				'menu_harian' => $menu ? $menu->nama : '-',
				'waktu_update' => $menu ? $this->formatWaktu($menu->updated_at) : '-',
				'status_laporan' => $statusLaporan,
				'detail_url' => url('/sppg/detail/'.Hashids::encode($sppg->id)),
				'search_text' => implode(' ', [
					$sppg->nama,
					$sppg->alamat,
					$this->lokasi($sppg),
				]),
			];
		});

		$rows = $this->filterRows($rows, $status, $search);

		return [
			'totalTarget' => $sppgs->count(),
			'totalSudahLapor' => $totalSudahLapor,
			'totalBelumLapor' => max($sppgs->count() - $totalSudahLapor, 0),
			'rows' => $rows,
		];
	}

	private function targetSekolahIds()
	{
		return Sekolah::where('status_layanan', 1)->pluck('id');
	}

	private function laporanSekolahBySekolah(Carbon $tanggal)
	{
		if (!class_exists(LaporanSekolah::class) || !Schema::hasTable('laporan_sekolahs')) {
			return collect();
		}

		return LaporanSekolah::with(['sppg'])
			->whereDate('tanggal', $tanggal->toDateString())
			->orderByDesc('updated_at')
			->orderByDesc('id')
			->get()
			->groupBy('sekolah_id')
			->map(function ($items) {
				return $items->first();
			});
	}

	private function menuHarianBySppg(Carbon $tanggal)
	{
		if (!class_exists(MenuHarian::class) || !Schema::hasTable('menu_harians')) {
			return collect();
		}

		return MenuHarian::whereDate('tanggal', $tanggal->toDateString())
			->orderByDesc('updated_at')
			->orderByDesc('id')
			->get()
			->groupBy('sppg_id')
			->map(function ($items) {
				return $items->first();
			});
	}

	private function filterRows(Collection $rows, $status, $search)
	{
		if ($status !== 'rekap') {
			$label = $status === 'sudah' ? 'Sudah Lapor' : 'Belum Lapor';
			$rows = $rows->filter(function ($row) use ($label) {
				return $row['status_laporan'] === $label;
			});
		}

		if ($search !== '') {
			$needle = Str::lower($search);
			$rows = $rows->filter(function ($row) use ($needle) {
				return Str::contains(Str::lower($row['search_text']), $needle);
			});
		}

		return $rows->values();
	}

	private function paginateCollection(Collection $rows, Request $request)
	{
		$perPage = 25;
		$page = LengthAwarePaginator::resolveCurrentPage();
		$items = $rows->slice(($page - 1) * $perPage, $perPage)->values();

		return new LengthAwarePaginator(
			$items,
			$rows->count(),
			$perPage,
			$page,
			[
				'path' => $request->url(),
				'query' => $request->query(),
			]
		);
	}

	private function parseTanggal($tanggal)
	{
		try {
			return $tanggal ? Carbon::parse($tanggal) : Carbon::today();
		} catch (\Throwable $e) {
			return Carbon::today();
		}
	}

	private function lokasi($model)
	{
		$wilayah = optional($model->wilayah)->nama_wilayah;
		$parent = optional(optional($model->wilayah)->parent)->nama_wilayah;

		return collect([$wilayah, $parent])->filter()->implode(', ') ?: ($model->alamat ?: '-');
	}

	private function jenisSekolahLabel($jenisId)
	{
		$map = [
			1 => 'Posyandu',
			2 => 'KB',
			3 => 'TK/PAUD',
			4 => 'Sekolah Dasar',
			5 => 'Sekolah Menengah Pertama',
			6 => 'Sekolah Menengah Atas',
		];

		return $map[(int) $jenisId] ?? 'Jenis ID '.$jenisId;
	}

	private function uploadUrl($path)
	{
		if (!$path) {
			return null;
		}

		if (Str::startsWith($path, ['http://', 'https://'])) {
			return $path;
		}

		if (Str::startsWith($path, ['storage/', 'po-content/'])) {
			return asset($path);
		}

		return asset('po-content/uploads/'.$path);
	}

	private function formatWaktu($value)
	{
		if (!$value) {
			return '-';
		}

		try {
			return Carbon::parse($value)->translatedFormat('d M Y H:i');
		} catch (\Throwable $e) {
			return '-';
		}
	}

	private function emptyData()
	{
		return [
			'totalTarget' => 0,
			'totalSudahLapor' => 0,
			'totalBelumLapor' => 0,
			'rows' => collect(),
		];
	}
}
