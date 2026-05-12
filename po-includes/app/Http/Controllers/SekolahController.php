<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Artesaos\SEOTools\Facades\SEOTools;
use Carbon\Carbon;
use Vinkla\Hashids\Facades\Hashids;
use App\Sekolah;
use App\Wilayah;
use App\Penerima;
use App\MenuHarian;
use App\Distribusi;
use App\LaporanSekolah;

class SekolahController extends Controller
{
	/**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application pages.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
		
        $twitterid = explode('/', getSetting('twitter'));
		SEOTools::setTitle('Daftar Sekolah - '.getSetting('web_name'));
		SEOTools::setDescription('Daftar Sekolah - '.getSetting('web_description'));
		SEOTools::metatags()->setKeywords(explode(',', getSetting('web_keyword')));
		SEOTools::setCanonical(getSetting('web_url') . '/sekolah');
		SEOTools::opengraph()->setTitle('Daftar Sekolah - '.getSetting('web_name'));
		SEOTools::opengraph()->setDescription('Daftar Sekolah - '.getSetting('web_description'));
		SEOTools::opengraph()->setUrl(getSetting('web_url') . '/sekolah');
		SEOTools::opengraph()->setSiteName(getSetting('web_author'));
		SEOTools::opengraph()->addImage(asset('po-content/uploads/'.getSetting('logo')));
		SEOTools::twitter()->setSite('@'.$twitterid[count($twitterid)-1]);
		SEOTools::twitter()->setTitle('Daftar Sekolah - '.getSetting('web_name'));
		SEOTools::twitter()->setDescription('Daftar Sekolah - '.getSetting('web_description'));
		SEOTools::twitter()->setUrl(getSetting('web_url') . '/sekolah');
		SEOTools::twitter()->setImage(asset('po-content/uploads/'.getSetting('logo')));
		SEOTools::jsonLd()->setTitle('Daftar Sekolah - '.getSetting('web_name'));
		SEOTools::jsonLd()->setDescription('Daftar Sekolah - '.getSetting('web_description'));
		SEOTools::jsonLd()->setType('WebPage');
		SEOTools::jsonLd()->setUrl(getSetting('web_url') . '/sekolah');
		SEOTools::jsonLd()->setImage(asset('po-content/uploads/'.getSetting('logo')));
		
		// card 1
		// total semua siswa
		$totalSiswa = Sekolah::sum('jumlah_total');

		// siswa dari sekolah yg status_layanan = 1
		$totalSiswaMbg = Sekolah::where('status_layanan', 1)->sum('jumlah_total');

		$persenSiswa = $totalSiswa > 0 ? round(($totalSiswaMbg / $totalSiswa) * 100, 2) : 0;

		// card 2
		// total sekolah
		$totalSekolah = Sekolah::count();

		// sekolah yg sudah menerima MBG
		$sekolahMbg = Sekolah::where('status_layanan', 1)->count();

		$persenSekolah = $totalSekolah > 0 ? round(($sekolahMbg / $totalSekolah) * 100, 1) : 0;

		$totalPenerimaSiswa = $this->sumPenerimaByKategori('siswa');
		$totalPenerimaSiswaMbg = $this->sumPenerimaByKategori('siswa', true);

		$totalIbuBalita = Sekolah::whereIn('jenis_id', [1,2])->sum('jumlah_total');
		
		$totalIbuBalitaMbg = Sekolah::whereIn('jenis_id', [1,2])->where('status_layanan', 1)->sum('jumlah_total');
		
		$persenIbuBalita = $totalIbuBalita > 0 ? round(($totalIbuBalitaMbg / $totalIbuBalita) * 100, 1) : 0;

		$rincianIbuBalita = [
			'bumil' => [
				'label' => 'Bumil',
				'total' => $this->sumPenerimaByKategori('bumil'),
				'mbg' => $this->sumPenerimaByKategori('bumil', true),
			],
			'busui' => [
				'label' => 'Busui',
				'total' => $this->sumPenerimaByKategori('busui'),
				'mbg' => $this->sumPenerimaByKategori('busui', true),
			],
			'balita' => [
				'label' => 'Balita',
				'total' => $this->sumPenerimaByKategori('balita'),
				'mbg' => $this->sumPenerimaByKategori('balita', true),
			],
		];

		$query = Sekolah::with(['sppgs', 'wilayah']);

			// 🔍 SEARCH
			if ($request->search) {
				$query->where(function ($q) use ($request) {
					$q->where('nama', 'like', '%' . $request->search . '%')
					->orWhereHas('sppgs', function ($q2) use ($request) {
						$q2->where('nama', 'like', '%' . $request->search . '%');
					});
				});
			}

			// kecamatan (parent)
			if ($request->kecamatan) {
				$query->whereHas('wilayah', function ($q) use ($request) {
					$q->where('parent_id', $request->kecamatan);
				});
			}

			// kelurahan (child)
			if ($request->kelurahan) {
				$query->whereHas('wilayah', function ($q) use ($request) {
					$q->where('id', $request->kelurahan);
				});
			}

			$sekolahs = $query->paginate(10)->appends(request()->query());

			// statistik
			$totalSiswa = Sekolah::sum('jumlah_total');
			$totalSekolah = Sekolah::count();

			// dropdown
			$kecamatans = Wilayah::where('child_id', 0)->pluck('nama_wilayah', 'id');

			return view(getTheme('sekolah'), compact(
				'sekolahs',
				'totalSiswa',
				'totalSekolah',
				'kecamatans',

				'totalSiswa',
				'totalSiswaMbg',
				'persenSiswa',

				'totalSekolah',
				'sekolahMbg',
				'persenSekolah',
				'totalPenerimaSiswa',
				'totalPenerimaSiswaMbg',

				'totalIbuBalita',
				'totalIbuBalitaMbg',
				'persenIbuBalita',
				'rincianIbuBalita'
			));
	}

	private function sumPenerimaByKategori($kategori, $onlyMbg = false)
	{
		$query = Penerima::where('kategori', $kategori);

		if ($onlyMbg) {
			$query->whereHas('sekolah', function ($q) {
				$q->where('status_layanan', 1);
			});
		}

		return (int) $query->sum('jumlah');
	}

	public function detail(Request $request, $id)
	{
		$ids = Hashids::decode($id);
		$sekolahId = count($ids) ? $ids[0] : $id;

		$sekolah = Sekolah::with([
			'wilayah.parent',
			'penerimas',
			'sppgs.wilayah.parent',
			'distribusis.menuHarian',
			'distribusis.sppg',
		])->findOrFail($sekolahId);

		$twitterid = explode('/', getSetting('twitter'));
		SEOTools::setTitle($sekolah->nama.' - '.getSetting('web_name'));
		SEOTools::setDescription('Detail sekolah penerima manfaat MBG - '.getSetting('web_description'));
		SEOTools::metatags()->setKeywords(explode(',', getSetting('web_keyword')));
		SEOTools::setCanonical(getSetting('web_url') . '/sekolah/detail/' . $id);
		SEOTools::opengraph()->setTitle($sekolah->nama.' - '.getSetting('web_name'));
		SEOTools::opengraph()->setDescription('Detail sekolah penerima manfaat MBG');
		SEOTools::opengraph()->setUrl(getSetting('web_url') . '/sekolah/detail/' . $id);
		SEOTools::opengraph()->setSiteName(getSetting('web_author'));
		SEOTools::opengraph()->addImage(asset('po-content/uploads/'.getSetting('logo')));
		SEOTools::twitter()->setSite('@'.$twitterid[count($twitterid)-1]);
		SEOTools::twitter()->setTitle($sekolah->nama.' - '.getSetting('web_name'));
		SEOTools::twitter()->setDescription('Detail sekolah penerima manfaat MBG');
		SEOTools::twitter()->setUrl(getSetting('web_url') . '/sekolah/detail/' . $id);
		SEOTools::twitter()->setImage(asset('po-content/uploads/'.getSetting('logo')));
		SEOTools::jsonLd()->setTitle($sekolah->nama.' - '.getSetting('web_name'));
		SEOTools::jsonLd()->setDescription('Detail sekolah penerima manfaat MBG');
		SEOTools::jsonLd()->setType('WebPage');
		SEOTools::jsonLd()->setUrl(getSetting('web_url') . '/sekolah/detail/' . $id);
		SEOTools::jsonLd()->setImage(asset('po-content/uploads/'.getSetting('logo')));

		$penerimas = $sekolah->penerimas->pluck('jumlah', 'kategori');
		$sppgIds = $sekolah->sppgs->pluck('id');
		$today = Carbon::today()->toDateString();

		$menuHariIni = MenuHarian::with('sppg')
			->whereDate('tanggal', $today)
			->whereIn('sppg_id', $sppgIds)
			->latest('tanggal')
			->first();

		$laporanSekolahHariIni = Schema::hasTable('laporan_sekolahs')
			? LaporanSekolah::with(['menuHarian', 'sppg', 'distribusi'])
				->where('sekolah_id', $sekolah->id)
				->whereDate('tanggal', $today)
				->latest('waktu_upload')
				->latest('id')
				->first()
			: null;

		if (!$menuHariIni && $laporanSekolahHariIni && $laporanSekolahHariIni->menuHarian) {
			$menuHariIni = $laporanSekolahHariIni->menuHarian;
		}

		$distribusiTerakhir = Distribusi::with(['menuHarian', 'sppg', 'laporanSekolahs'])
			->where('sekolah_id', $sekolah->id)
			->whereDate('tanggal', $today)
			->latest('tanggal')
			->latest('id')
			->first();

		$activeRiwayatTab = in_array($request->input('riwayat_tab'), ['distribusi', 'laporan'])
			? $request->input('riwayat_tab')
			: 'laporan';
		$riwayatTanggal = $request->input('riwayat_tanggal', $today);
		try {
			$riwayatTanggal = Carbon::parse($riwayatTanggal)->toDateString();
		} catch (\Exception $e) {
			$riwayatTanggal = $today;
		}

		$riwayatDistribusiQuery = Distribusi::with(['menuHarian', 'sppg', 'laporanSekolahs'])
			->where('sekolah_id', $sekolah->id)
			->whereDate('tanggal', $riwayatTanggal);

		$riwayatDistribusi = $riwayatDistribusiQuery
			->latest('tanggal')
			->latest('id')
			->paginate(5, ['*'], 'distribusi_page')
			->appends($request->query());

		$riwayatLaporanSekolah = Schema::hasTable('laporan_sekolahs')
			? LaporanSekolah::with(['menuHarian', 'sppg'])
				->where('sekolah_id', $sekolah->id)
				->whereDate('tanggal', $riwayatTanggal)
				->latest('tanggal')
				->latest('id')
				->paginate(5, ['*'], 'laporan_page')
				->appends($request->query())
			: collect();

		$sppgUtama = ($distribusiTerakhir ? $distribusiTerakhir->sppg : null)
			?? ($laporanSekolahHariIni ? $laporanSekolahHariIni->sppg : null)
			?? ($menuHariIni ? $menuHariIni->sppg : null)
			?? $sekolah->sppgs->first();
		$statusDistribusi = $this->statusDistribusiLabel($distribusiTerakhir, $laporanSekolahHariIni);
		$jenisSekolah = $this->jenisSekolahLabel($sekolah->jenis_id);
		$totalPorsiHariIni = $distribusiTerakhir ? $distribusiTerakhir->jumlah_porsi : ($laporanSekolahHariIni ? $sekolah->jumlah_total : 0);
		$tanggalLaporanHariIni = $distribusiTerakhir ? $distribusiTerakhir->tanggal : ($laporanSekolahHariIni ? $laporanSekolahHariIni->tanggal : null);

		return view(getTheme('detailsekolah'), compact(
			'sekolah',
			'jenisSekolah',
			'penerimas',
			'menuHariIni',
			'laporanSekolahHariIni',
			'distribusiTerakhir',
			'riwayatDistribusi',
			'riwayatLaporanSekolah',
			'activeRiwayatTab',
			'riwayatTanggal',
			'sppgUtama',
			'statusDistribusi',
			'totalPorsiHariIni',
			'tanggalLaporanHariIni'
		));
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

	private function statusDistribusiLabel($distribusi, $laporanSekolah = null)
	{
		if ((!$distribusi || !$distribusi->tanggal || !$distribusi->tanggal->isToday()) && !$laporanSekolah) {
			return [
				'label' => 'Belum ada laporan distribusi hari ini',
				'badge' => 'bg-surface-container text-on-surface',
				'time' => '-',
			];
		}

		if (!$distribusi && $laporanSekolah) {
			return [
				'label' => 'Sudah Lapor Sekolah',
				'badge' => 'bg-secondary-container text-on-secondary-container',
				'time' => $laporanSekolah->waktu_upload ? $laporanSekolah->waktu_upload->format('H:i').' WIB' : '-',
			];
		}

		if ((int) $distribusi->status_distribusi > 1) {
			return [
				'label' => 'Sudah Lapor Sekolah',
				'badge' => 'bg-secondary-container text-on-secondary-container',
				'time' => $distribusi->updated_at ? $distribusi->updated_at->format('H:i').' WIB' : '-',
			];
		}

		return [
			'label' => 'Belum Lapor Sekolah',
			'badge' => 'bg-yellow-100 text-yellow-800',
			'time' => '-',
		];
	}

}
