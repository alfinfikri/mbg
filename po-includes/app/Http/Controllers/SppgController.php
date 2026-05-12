<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Artesaos\SEOTools\Facades\SEOTools;
use Vinkla\Hashids\Facades\Hashids;
use App\Sppg;
use App\Sekolah;
use App\MenuHarian;
use App\Distribusi;
use App\LaporanSekolah;

class SppgController extends Controller
{
	public function index(Request $request)
	{
		SEOTools::setTitle('Daftar SPPG - '.getSetting('web_name'));
		SEOTools::setDescription('Daftar Satuan Pelayanan Pemenuhan Gizi - '.getSetting('web_description'));

		$query = Sppg::with(['wilayah.parent', 'sekolahs'])
			->withCount('sekolahs');

		if ($request->filled('search')) {
			$search = $request->search;
			$query->where(function ($q) use ($search) {
				$q->where('nama', 'like', '%'.$search.'%')
					->orWhere('alamat', 'like', '%'.$search.'%')
					->orWhere('nama_penanggung_jawab', 'like', '%'.$search.'%');
			});
		}

		if ($request->filled('status_layanan')) {
			$query->where('status_layanan', $request->status_layanan);
		}

		$sppgs = $query->orderBy('nama')->paginate(10)->appends($request->query());

		$totalSppg = Sppg::count();
		$totalSppgAktif = Sppg::where('status_layanan', 1)->count();
		$totalSekolahDilayani = Sekolah::whereHas('sppgs')->count();
		$totalPenerima = Sekolah::whereHas('sppgs')->sum('jumlah_total');
		return view(getTheme('sppg'), compact(
			'sppgs',
			'totalSppg',
			'totalSppgAktif',
			'totalSekolahDilayani',
			'totalPenerima'
		));
	}

	public function detail(Request $request, $id)
	{
		$ids = Hashids::decode($id);
		$sppgId = count($ids) ? $ids[0] : $id;

		$sppg = Sppg::with(['wilayah.parent', 'sekolahs.wilayah.parent'])->findOrFail($sppgId);
		SEOTools::setTitle($sppg->nama.' - '.getSetting('web_name'));
		SEOTools::setDescription('Detail SPPG MBG - '.getSetting('web_description'));

		$totalSekolah = $sppg->sekolahs->count();
		$totalPenerima = $sppg->sekolahs->sum('jumlah_total');
		$today = now()->toDateString();

		$menuTerbaru = MenuHarian::where('sppg_id', $sppg->id)
			->whereDate('tanggal', $today)
			->latest('id')
			->first();

		$ringkasanDistribusi = [
			'total' => Distribusi::where('sppg_id', $sppg->id)->whereDate('tanggal', $today)->count(),
			'hari_ini' => Distribusi::where('sppg_id', $sppg->id)->whereDate('tanggal', $today)->count(),
			'porsi_hari_ini' => Distribusi::where('sppg_id', $sppg->id)->whereDate('tanggal', $today)->sum('jumlah_porsi'),
			'sudah_lapor' => Distribusi::where('sppg_id', $sppg->id)->whereDate('tanggal', $today)->where('status_distribusi', '>', 1)->count(),
			'belum_lapor' => Distribusi::where('sppg_id', $sppg->id)->whereDate('tanggal', $today)->where('status_distribusi', 1)->count(),
		];

		$ringkasanLaporanSekolah = [
			'total' => 0,
			'hari_ini' => 0,
			'terverifikasi' => 0,
		];

		$hasLaporanSekolahTable = Schema::hasTable('laporan_sekolahs');

		if ($hasLaporanSekolahTable) {
			$ringkasanLaporanSekolah = [
				'total' => LaporanSekolah::where('sppg_id', $sppg->id)->whereDate('tanggal', $today)->count(),
				'hari_ini' => LaporanSekolah::where('sppg_id', $sppg->id)->whereDate('tanggal', $today)->count(),
				'terverifikasi' => LaporanSekolah::where('sppg_id', $sppg->id)->whereDate('tanggal', $today)->where('status_laporan', 2)->count(),
			];
		}

		$riwayatDistribusiRelations = ['sekolah.wilayah.parent', 'menuHarian'];
		if ($hasLaporanSekolahTable) {
			$riwayatDistribusiRelations[] = 'laporanSekolahs';
		}

		$riwayatTanggal = $request->input('riwayat_tanggal', $today);
		try {
			$riwayatTanggal = \Carbon\Carbon::parse($riwayatTanggal)->toDateString();
		} catch (\Exception $e) {
			$riwayatTanggal = $today;
		}

		$riwayatDistribusi = Distribusi::with($riwayatDistribusiRelations)
			->where('sppg_id', $sppg->id)
			->whereDate('tanggal', $riwayatTanggal)
			->latest('tanggal')
			->latest('id')
			->paginate(10, ['*'], 'distribusi_page')
			->appends($request->query());

		return view(getTheme('detailsppg'), compact(
			'sppg',
			'totalSekolah',
			'totalPenerima',
			'menuTerbaru',
			'ringkasanDistribusi',
			'ringkasanLaporanSekolah',
			'riwayatDistribusi',
			'hasLaporanSekolahTable',
			'riwayatTanggal'
		));
	}

}
