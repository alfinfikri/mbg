<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Artesaos\SEOTools\Facades\SEOTools;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Aduan;
use App\Distribusi;
use App\LaporanSekolah;
use App\MenuHarian;
use App\Post;
use App\Sekolah;
use App\Sppg;
use App\ViewPage;

class HomeController extends Controller
{
	public function index(Request $request)
	{
		$this->seo();
		$this->recordVisit($request);

		$stats = [
			'total_sekolah' => $this->countModel(Sekolah::class, 'sekolahs'),
			'total_sppg' => $this->countModel(Sppg::class, 'sppgs'),
			'total_menu_harian' => $this->countModel(MenuHarian::class, 'menu_harians'),
			'total_laporan_sekolah' => $this->countModel(LaporanSekolah::class, 'laporan_sekolahs'),
			'total_distribusi' => $this->countModel(Distribusi::class, 'distribusis'),
			'total_aduan' => $this->countAduan(),
		];

		$ringkasanHariIni = $this->ringkasanHariIni();
		$menuHarians = $this->menuHarians();
		$sppgAktifs = $this->sppgAktifs();
		$sekolahAktifs = $this->sekolahAktifs();
		$beritas = $this->beritas();
		$laporanProgram = $this->laporanProgramHariIni();

		return view('frontend.home.index', compact(
			'stats',
			'ringkasanHariIni',
			'menuHarians',
			'sppgAktifs',
			'sekolahAktifs',
			'beritas',
			'laporanProgram'
		));
	}

	private function seo()
	{
		$twitterid = explode('/', getSetting('twitter'));
		SEOTools::setTitle(getSetting('web_name'));
		SEOTools::setDescription(getSetting('web_description'));
		SEOTools::metatags()->setKeywords(explode(',', getSetting('web_keyword')));
		SEOTools::setCanonical(getSetting('web_url'));
		SEOTools::opengraph()->setTitle(getSetting('web_name'));
		SEOTools::opengraph()->setDescription(getSetting('web_description'));
		SEOTools::opengraph()->setUrl(getSetting('web_url'));
		SEOTools::opengraph()->setSiteName(getSetting('web_author'));
		SEOTools::opengraph()->addImage(asset('po-content/uploads/'.getSetting('logo')));
		SEOTools::twitter()->setSite('@'.$twitterid[count($twitterid)-1]);
		SEOTools::twitter()->setTitle(getSetting('web_name'));
		SEOTools::twitter()->setDescription(getSetting('web_description'));
		SEOTools::twitter()->setUrl(getSetting('web_url'));
		SEOTools::twitter()->setImage(asset('po-content/uploads/'.getSetting('logo')));
		SEOTools::jsonLd()->setTitle(getSetting('web_name'));
		SEOTools::jsonLd()->setDescription(getSetting('web_description'));
		SEOTools::jsonLd()->setType('WebPage');
		SEOTools::jsonLd()->setUrl(getSetting('web_url'));
		SEOTools::jsonLd()->setImage(asset('po-content/uploads/'.getSetting('logo')));
	}

	private function recordVisit(Request $request)
	{
		if (!class_exists(ViewPage::class) || !Schema::hasTable('shetabit_visits')) {
			return;
		}

		$visit = ViewPage::select('ip')->where('ip', strip_tags($request->ip()))->count();
		if ($visit < 1) {
			visitor()->visit();
		}
	}

	private function countModel($class, $table)
	{
		if (!class_exists($class) || !Schema::hasTable($table)) {
			return 0;
		}

		try {
			return $class::count();
		} catch (\Throwable $e) {
			return 0;
		}
	}

	private function countAduan()
	{
		if (class_exists(Aduan::class) && Schema::hasTable('aduans')) {
			return Aduan::count();
		}

		if (Schema::hasTable('pengaduans')) {
			return DB::table('pengaduans')->count();
		}

		return 0;
	}

	private function ringkasanHariIni()
	{
		$today = now()->toDateString();
		$distribusi = [
			'total_distribusi' => 0,
			'sudah_lapor' => 0,
			'belum_lapor' => 0,
			'total_porsi' => 0,
		];

		if (class_exists(Distribusi::class) && Schema::hasTable('distribusis')) {
			$query = Distribusi::whereDate('tanggal', $today);
			$distribusi = [
				'total_distribusi' => (clone $query)->count(),
				'sudah_lapor' => (clone $query)->where('status_distribusi', '>', 1)->count(),
				'belum_lapor' => (clone $query)->where('status_distribusi', 1)->count(),
				'total_porsi' => (clone $query)->sum('jumlah_porsi'),
			];
		}

		$laporan = [
			'total_laporan' => 0,
			'terverifikasi' => 0,
		];

		if (class_exists(LaporanSekolah::class) && Schema::hasTable('laporan_sekolahs')) {
			$query = LaporanSekolah::whereDate('tanggal', $today);
			$laporan = [
				'total_laporan' => (clone $query)->count(),
				'terverifikasi' => (clone $query)->where('status_laporan', 2)->count(),
			];
		}

		return array_merge($distribusi, $laporan);
	}

	private function menuHarians()
	{
		if (!class_exists(MenuHarian::class) || !Schema::hasTable('menu_harians')) {
			return collect();
		}

		return MenuHarian::with('sppg')
			->latest('tanggal')
			->latest('id')
			->limit(3)
			->get();
	}

	private function sppgAktifs()
	{
		if (!class_exists(Sppg::class) || !Schema::hasTable('sppgs')) {
			return collect();
		}

		return Sppg::with('wilayah.parent')
			->withCount('sekolahs')
			->where('status_layanan', 1)
			->orderBy('nama')
			->limit(4)
			->get();
	}

	private function sekolahAktifs()
	{
		if (!class_exists(Sekolah::class) || !Schema::hasTable('sekolahs')) {
			return collect();
		}

		return Sekolah::with('wilayah.parent')
			->where('status_layanan', 1)
			->orderBy('nama')
			->limit(4)
			->get();
	}

	private function beritas()
	{
		if (!class_exists(Post::class) || !Schema::hasTable('posts')) {
			return collect();
		}

		return Post::where('active', 'Y')
			->latest('tanggal')
			->latest('id')
			->limit(3)
			->get();
	}

	private function laporanProgramHariIni()
	{
		$today = Carbon::today();
		$totalSppgAktif = $this->totalSppgAktif();
		$laporanSppgHariIni = $this->laporanSppgHariIni($today);
		$laporanSekolahHariIni = $this->laporanSekolahHariIni($today);
		$totalTargetSekolahHariIni = $this->totalTargetSekolahHariIni($today);
		$totalPorsiHariIni = $this->totalPorsiHariIni($today);

		return [
			'tanggal_hari_ini' => $today->translatedFormat('l, d F Y'),
			'laporan_sppg_hari_ini' => $laporanSppgHariIni,
			'total_sppg_aktif' => $totalSppgAktif,
			'persen_laporan_sppg' => $this->percentage($laporanSppgHariIni, $totalSppgAktif),
			'laporan_sekolah_hari_ini' => $laporanSekolahHariIni,
			'total_target_sekolah_hari_ini' => $totalTargetSekolahHariIni,
			'persen_laporan_sekolah' => $this->percentage($laporanSekolahHariIni, $totalTargetSekolahHariIni),
			'sppg_belum_lapor' => max($totalSppgAktif - $laporanSppgHariIni, 0),
			'sekolah_belum_lapor' => max($totalTargetSekolahHariIni - $laporanSekolahHariIni, 0),
			'total_porsi_hari_ini' => $totalPorsiHariIni,
		];
	}

	private function totalSppgAktif()
	{
		if (!class_exists(Sppg::class) || !Schema::hasTable('sppgs')) {
			return 0;
		}

		return Sppg::where('status_layanan', 1)->count();
	}

	private function laporanSppgHariIni(Carbon $date)
	{
		if (!class_exists(MenuHarian::class) || !Schema::hasTable('menu_harians')) {
			return 0;
		}

		return MenuHarian::whereDate('tanggal', $date->toDateString())->count();
	}

	private function laporanSekolahHariIni(Carbon $date)
	{
		if (!class_exists(LaporanSekolah::class) || !Schema::hasTable('laporan_sekolahs')) {
			return 0;
		}

		return LaporanSekolah::whereDate('tanggal', $date->toDateString())->count();
	}

	private function totalTargetSekolahHariIni(Carbon $date)
	{
		if (!class_exists(Sekolah::class) || !Schema::hasTable('sekolahs')) {
			return 0;
		}

		return Sekolah::where('status_layanan', 1)->count();
	}

	private function totalPorsiHariIni(Carbon $date)
	{
		if (!class_exists(Distribusi::class) || !Schema::hasTable('distribusis')) {
			return 0;
		}

		return Distribusi::whereDate('tanggal', $date->toDateString())->sum('jumlah_porsi');
	}

	private function percentage($value, $target)
	{
		if ($target <= 0) {
			return 0;
		}

		return round(($value / $target) * 100, 1);
	}
}
