<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Artesaos\SEOTools\Facades\SEOTools;
use Shetabit\Visitor\Traits\Visitor;
use App\Post;
use App\MenuHarian;
use App\Subscribe;
use App\ViewPage;
use App\Distribusi;
use App\LaporanSekolah;
use App\Sekolah;
use App\Sppg;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
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
     * Show the application home.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
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

		// Check duplicates IP
        $visit = ViewPage::select('ip')->where('ip', strip_tags($request->ip()))->count();

		$todayMenu = MenuHarian::with('sppg')
			->whereDate('tanggal', now()->toDateString())
			->latest('id')
			->first();

		if (!$todayMenu) {
			$todayMenu = MenuHarian::with('sppg')->latest('tanggal')->latest('id')->first();
		}

		$stats = [
			'total_sekolah' => Sekolah::count(),
			'total_sppg' => Sppg::count(),
			'total_penerima' => Sekolah::sum('jumlah_total'),
			'total_menu' => MenuHarian::count(),
		];

		$distribusiHariIniQuery = Distribusi::whereDate('tanggal', now()->toDateString());
		$ringkasanHariIni = [
			'total_distribusi' => (clone $distribusiHariIniQuery)->count(),
			'sudah_lapor' => (clone $distribusiHariIniQuery)->where('status_distribusi', '>', 1)->count(),
			'belum_lapor' => (clone $distribusiHariIniQuery)->where('status_distribusi', 1)->count(),
			'total_porsi' => (clone $distribusiHariIniQuery)->sum('jumlah_porsi'),
			'laporan_sekolah' => Schema::hasTable('laporan_sekolahs')
				? LaporanSekolah::whereDate('tanggal', now()->toDateString())->count()
				: 0,
		];

        if ($visit >= 1) {
            return view(getTheme('home'), compact('todayMenu', 'stats', 'ringkasanHariIni'));
        }else{
			visitor()->visit(); // create a visit log
			return view(getTheme('home'), compact('todayMenu', 'stats', 'ringkasanHariIni'));
		}
    }
	
	public function error404()
    {
		$twitterid = explode('/', getSetting('twitter'));
		SEOTools::setTitle('Not Found - '.getSetting('web_name'));
		SEOTools::setDescription(getSetting('web_description'));
		SEOTools::metatags()->setKeywords(explode(',', getSetting('web_keyword')));
		SEOTools::setCanonical(getSetting('web_url') . '/404');
		SEOTools::opengraph()->setTitle('Not Found - '.getSetting('web_name'));
		SEOTools::opengraph()->setDescription(getSetting('web_description'));
		SEOTools::opengraph()->setUrl(getSetting('web_url') . '/404');
		SEOTools::opengraph()->setSiteName(getSetting('web_author'));
		SEOTools::opengraph()->addImage(asset('po-content/uploads/'.getSetting('logo')));
		SEOTools::twitter()->setSite('@'.$twitterid[count($twitterid)-1]);
		SEOTools::twitter()->setTitle('Not Found - '.getSetting('web_name'));
		SEOTools::twitter()->setDescription(getSetting('web_description'));
		SEOTools::twitter()->setUrl(getSetting('web_url') . '/404');
		SEOTools::twitter()->setImage(asset('po-content/uploads/'.getSetting('logo')));
		SEOTools::jsonLd()->setTitle('Not Found - '.getSetting('web_name'));
		SEOTools::jsonLd()->setDescription(getSetting('web_description'));
		SEOTools::jsonLd()->setType('WebPage');
		SEOTools::jsonLd()->setUrl(getSetting('web_url') . '/404');
		SEOTools::jsonLd()->setImage(asset('po-content/uploads/'.getSetting('logo')));
		
		return response()->view(getTheme('404'), [], 404);
	}
	
	public function subscribe(Request $request)
    {
		$this->validate($request,[
			'email' => 'required|string|max:255|email'
		]);
		
		$name = explode('@', $request->email);
		$finalname = ucfirst($name[0]);
		
		$request->request->add([
			'name' => $finalname,
			'created_by' => 1,
			'updated_by' => 1
		]);
		$requestData = $request->all();

		Subscribe::create($requestData);
		
		return redirect('contact')->with('flash_message', __('subscribe.send_notif'));
    }
}
