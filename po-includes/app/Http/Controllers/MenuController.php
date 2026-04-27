<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Artesaos\SEOTools\Facades\SEOTools;

use App\MenuHarian;

class MenuController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
		config([
			'captcha.secret' => getSetting('recaptcha_secret'),
			'captcha.sitekey' => getSetting('recaptcha_key'),
		]);
		
        // $this->middleware('auth');
    }

    /**
     * Show the application contact.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
		$twitterid = explode('/', getSetting('twitter'));
		SEOTools::setTitle('Menu & Gizi MBG - '.getSetting('web_name'));
		SEOTools::setDescription('Menu & Gizi MBG - '.getSetting('web_description'));
		SEOTools::metatags()->setKeywords(explode(',', getSetting('web_keyword')));
		SEOTools::setCanonical(getSetting('web_url') . '/menu');
		SEOTools::opengraph()->setTitle('Menu & Gizi MBG - '.getSetting('web_name'));
		SEOTools::opengraph()->setDescription('Menu & Gizi MBG - '.getSetting('web_description'));
		SEOTools::opengraph()->setUrl(getSetting('web_url') . '/menu');
		SEOTools::opengraph()->setSiteName(getSetting('web_author'));
		SEOTools::opengraph()->addImage(asset('po-content/uploads/'.getSetting('logo')));
		SEOTools::twitter()->setSite('@'.$twitterid[count($twitterid)-1]);
		SEOTools::twitter()->setTitle('Menu & Gizi MBG - '.getSetting('web_name'));
		SEOTools::twitter()->setDescription('Menu & Gizi MBG - '.getSetting('web_description'));
		SEOTools::twitter()->setUrl(getSetting('web_url') . '/menu');
		SEOTools::twitter()->setImage(asset('po-content/uploads/'.getSetting('logo')));
		SEOTools::jsonLd()->setTitle('Menu & Gizi MBG - '.getSetting('web_name'));
		SEOTools::jsonLd()->setDescription('Menu & Gizi MBG - '.getSetting('web_description'));
		SEOTools::jsonLd()->setType('WebPage');
		SEOTools::jsonLd()->setUrl(getSetting('web_url') . '/menu');
		SEOTools::jsonLd()->setImage(asset('po-content/uploads/'.getSetting('logo')));
		
        $todayMenu = MenuHarian::whereDate('tanggal', now())->first();

		// fallback kalau kosong → ambil terbaru
		if (!$todayMenu) {
			$todayMenu = MenuHarian::latest('tanggal')->first();
		}

		// MENU 1 MINGGU (Senin–Jumat)
		$weeklyMenus = MenuHarian::whereBetween('tanggal', [
			now()->startOfWeek(),
			now()->startOfWeek()->addDays(4)
		])
		->get()
		->sortByDesc('created_at')
		->values();

		// AMBIL DATA TERBARU (berdasarkan input terakhir)
		$latestMenu = $weeklyMenus->sortByDesc('created_at')->first();

		return view(getTheme('menu'), compact('todayMenu', 'weeklyMenus', 'latestMenu'));
    }
	
}
