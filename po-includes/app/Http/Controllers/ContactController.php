<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Artesaos\SEOTools\Facades\SEOTools;

use App\Aduan;
use App\Mail\AduanMasukMail;
use App\Wilayah;
use App\Activity;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
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
		SEOTools::setTitle('Kontak & Pengaduan - '.getSetting('web_name'));
		SEOTools::setDescription('Kontak & Pengaduan - '.getSetting('web_description'));
		SEOTools::metatags()->setKeywords(explode(',', getSetting('web_keyword')));
		SEOTools::setCanonical(getSetting('web_url') . '/contact');
		SEOTools::opengraph()->setTitle('Kontak & Pengaduan - '.getSetting('web_name'));
		SEOTools::opengraph()->setDescription('Kontak & Pengaduan - '.getSetting('web_description'));
		SEOTools::opengraph()->setUrl(getSetting('web_url') . '/contact');
		SEOTools::opengraph()->setSiteName(getSetting('web_author'));
		SEOTools::opengraph()->addImage(asset('po-content/uploads/'.getSetting('logo')));
		SEOTools::twitter()->setSite('@'.$twitterid[count($twitterid)-1]);
		SEOTools::twitter()->setTitle('Kontak & Pengaduan - '.getSetting('web_name'));
		SEOTools::twitter()->setDescription('Kontak & Pengaduan - '.getSetting('web_description'));
		SEOTools::twitter()->setUrl(getSetting('web_url') . '/contact');
		SEOTools::twitter()->setImage(asset('po-content/uploads/'.getSetting('logo')));
		SEOTools::jsonLd()->setTitle('Kontak & Pengaduan - '.getSetting('web_name'));
		SEOTools::jsonLd()->setDescription('Kontak & Pengaduan - '.getSetting('web_description'));
		SEOTools::jsonLd()->setType('WebPage');
		SEOTools::jsonLd()->setUrl(getSetting('web_url') . '/contact');
		SEOTools::jsonLd()->setImage(asset('po-content/uploads/'.getSetting('logo')));
		
		$kecamatans = Wilayah::where('child_id', 0)->pluck('nama_wilayah', 'id');

        return view(getTheme('contact'), compact('kecamatans'));
    }

	public function send(Request $request)
	{
		$this->validate($request, [
			'nama' => 'required|string|max:255',
			'no_hp' => 'nullable|string|max:30',
			'alamat' => 'required|string|max:100',
			'judul_aduan' => 'required',
			'isi_aduan' => 'required',
			'wilayah_id' => 'required|exists:wilayahs,id',
			'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
		]);

		$fotoPath = $this->storeFoto($request);

		// generate kode tiket
		do {
			$kodeTiket = 'MBG-' . date('Ymd') . '-' . strtoupper(Str::random(4));
		} while (Aduan::where('kode_tiket', $kodeTiket)->exists());

		$aduan = Aduan::create([
				'nama' => $request->nama,
				'no_hp' => $request->no_hp,
				'alamat' => $request->alamat,
				'judul_aduan' => $request->judul_aduan,
				'isi_aduan' => $request->isi_aduan,
				'wilayah_id' => $request->wilayah_id,
				'foto' => $fotoPath,
				'kode_tiket' => $kodeTiket,
				'tgl_aduan' => Carbon::now(),
				'status' => 0,
				'status_pengaduan' => 0,
				'created_by' => Auth::check() ? Auth::id() : null,
				'updated_by' => Auth::check() ? Auth::id() : null,
			]);

		activity()->performedOn($aduan)
				->withProperties([
					'keterangan' => 'Laporan telah masuk ke sistem kami',
					'kode_tiket' => $aduan->kode_tiket
				])
        		->log('Laporan Diterima');

		$this->sendAduanNotification($aduan);

		return redirect('contact')
			->with('flash_message', 'Pengaduan berhasil dikirim')
			->with('kode_tiket', $kodeTiket);
	}

	public function tracking(Request $request)
	{
		$twitterid = explode('/', getSetting('twitter'));
		SEOTools::setTitle('Tracking Aduan - '.getSetting('web_name'));
		SEOTools::setDescription('Tracking Aduan - '.getSetting('web_description'));
		SEOTools::metatags()->setKeywords(explode(',', getSetting('web_keyword')));
		SEOTools::setCanonical(getSetting('web_url') . '/contact');
		SEOTools::opengraph()->setTitle('Tracking Aduan - '.getSetting('web_name'));
		SEOTools::opengraph()->setDescription('Tracking Aduan - '.getSetting('web_description'));
		SEOTools::opengraph()->setUrl(getSetting('web_url') . '/tracking');
		SEOTools::opengraph()->setSiteName(getSetting('web_author'));
		SEOTools::opengraph()->addImage(asset('po-content/uploads/'.getSetting('logo')));
		SEOTools::twitter()->setSite('@'.$twitterid[count($twitterid)-1]);
		SEOTools::twitter()->setTitle('Tracking Aduan - '.getSetting('web_name'));
		SEOTools::twitter()->setDescription('Tracking Aduan - '.getSetting('web_description'));
		SEOTools::twitter()->setUrl(getSetting('web_url') . '/contact');
		SEOTools::twitter()->setImage(asset('po-content/uploads/'.getSetting('logo')));
		SEOTools::jsonLd()->setTitle('Tracking Aduan - '.getSetting('web_name'));
		SEOTools::jsonLd()->setDescription('Tracking Aduan - '.getSetting('web_description'));
		SEOTools::jsonLd()->setType('WebPage');
		SEOTools::jsonLd()->setUrl(getSetting('web_url') . '/tracking');
		SEOTools::jsonLd()->setImage(asset('po-content/uploads/'.getSetting('logo')));

		$aduan = null;
		$logs = collect();

		if ($request->kode_tiket) {

			$aduan = Aduan::with(['wilayah','sppg','disposisiSppg','disposisiSatgas','closedBy'])
				->where('kode_tiket', $request->kode_tiket)
				->first();

			if ($aduan) {
				$logs = Activity::where('subject_type', Aduan::class)
					->where('subject_id', $aduan->id)
					->latest()
					->get();
			}
		}

		return view(getTheme('tracking'), compact('aduan', 'logs'));
	}

	private function storeFoto(Request $request)
	{
		if (!$request->hasFile('foto')) {
			return null;
		}

		$file = $request->file('foto');
		$destination = $this->uploadPath('aduan');
		$filename = time().'_'.uniqid().'_'.str_replace(' ', '-', $file->getClientOriginalName());

		if (!File::isDirectory($destination)) {
			File::makeDirectory($destination, 0777, true, true);
		}

		$file->move($destination, $filename);

		return 'aduan/'.$filename;
	}

	private function sendAduanNotification(Aduan $aduan)
	{
		try {
			Mail::to('satgasmbg@serangkota.go.id')
				->send(new AduanMasukMail($aduan));
		} catch (\Exception $e) {
			Log::warning('Gagal mengirim email notifikasi aduan baru', [
				'aduan_id' => $aduan->id,
				'kode_tiket' => $aduan->kode_tiket,
				'error' => $e->getMessage(),
			]);
		}
	}

	private function uploadPath($path = '')
	{
		return str_replace(['\po-includes','/po-includes'], '', base_path('po-content/uploads/'.$path));
	}
}
