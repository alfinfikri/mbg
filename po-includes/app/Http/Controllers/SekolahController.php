<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Artesaos\SEOTools\Facades\SEOTools;
use App\Sekolah;
use App\Wilayah;

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

		$totalIbuBalita = Sekolah::whereIn('jenis_id', [1,2])->sum('jumlah_total');
		
		$totalIbuBalitaMbg = Sekolah::whereIn('jenis_id', [1,2])->where('status_layanan', 1)->sum('jumlah_total');
		
		$persenIbuBalita = $totalIbuBalita > 0 ? round(($totalIbuBalitaMbg / $totalIbuBalita) * 100, 1) : 0;

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

				'totalIbuBalita',
				'totalIbuBalitaMbg',
				'persenIbuBalita'
			));
	}

}
