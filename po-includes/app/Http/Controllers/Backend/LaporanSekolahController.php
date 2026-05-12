<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Yajra\Datatables\Datatables;
use Vinkla\Hashids\Facades\Hashids;

use App\Distribusi;
use App\LaporanSekolah;
use App\MenuHarian;
use App\Sekolah;
use App\Sppg;

class LaporanSekolahController extends Controller
{
	public function index()
	{
		if (!$this->canRead()) {
			return redirect('forbidden');
		}

		return view('backend.laporansekolah.datatable');
	}

	public function getIndex()
	{
		return $this->index();
	}

	public function anyData(Request $request)
	{
		if (!$this->canRead()) {
			abort(403);
		}

		$laporans = $this->applyFilters(
			$this->visibleLaporans(LaporanSekolah::with(['sekolah', 'sppg', 'menuHarian'])),
			$request
		)->orderBy('tanggal', 'desc')->orderBy('id', 'desc');

		return Datatables::of($laporans)
			->addColumn('check', function ($laporan) {
				return '<div style="text-align:center;">
					<input type="checkbox" />
					<input type="hidden" class="deldata" name="id[]" value="'.Hashids::encode($laporan->id).'" disabled />
				</div>';
			})
			->addColumn('tanggal', fn($s) => optional($s->tanggal)->format('d-m-Y') ?: '-')
			->addColumn('sekolah', fn($s) => optional($s->sekolah)->nama ?? '-')
			->addColumn('sppg', fn($s) => optional($s->sppg)->nama ?? '-')
			->addColumn('menu_harian', fn($s) => optional($s->menuHarian)->nama ?? '-')
			->addColumn('rating', fn($s) => $this->ratingStars($s->rating))
			->addColumn('foto_menu', fn($s) => $this->thumbnail($s->foto_menu))
			->addColumn('foto_siswa', fn($s) => $this->thumbnail($s->foto_siswa))
			->addColumn('status_laporan', fn($s) => $this->statusBadge($s->status_laporan))
			->addColumn('action', function ($laporan) {
				$hash = Hashids::encode($laporan->id);
				$btn = '<div style="text-align:center;"><div class="btn-group">';
				$btn .= '<a href="'.url('dashboard/laporan-sekolahs/'.$hash).'" class="btn btn-info btn-xs btn-icon" title="'.__('general.show').'" data-toggle="tooltip" data-placement="left"><i class="fa fa-eye"></i></a>';

				if ($this->canUpdateLaporan($laporan)) {
					$btn .= '<a href="'.url('dashboard/laporan-sekolahs/'.$hash.'/edit').'" class="btn btn-primary btn-xs btn-icon" title="'.__('general.edit').'" data-toggle="tooltip" data-placement="left"><i class="fa fa-edit"></i></a>';
				}

				if ($this->canVerify()) {
					$btn .= '<a href="'.url('dashboard/laporan-sekolahs/'.$hash).'" class="btn btn-success btn-xs btn-icon" data-verify="" title="Verifikasi" data-toggle="tooltip" data-placement="left"><i class="fa fa-check"></i></a>';
					$btn .= '<a href="'.url('dashboard/laporan-sekolahs/'.$hash).'" class="btn btn-warning btn-xs btn-icon" data-reject="" title="Tolak" data-toggle="tooltip" data-placement="left"><i class="fa fa-times"></i></a>';
				}

				if ($this->canDeleteLaporan($laporan)) {
					$btn .= '<a href="'.url('dashboard/laporan-sekolahs/'.$hash).'" class="btn btn-danger btn-xs btn-icon" data-delete="" title="'.__('general.delete').'" data-toggle="tooltip" data-placement="left"><i class="fa fa-trash"></i></a>';
				}

				$btn .= '</div></div>';
				return $btn;
			})
			->escapeColumns([])
			->rawColumns(['check', 'rating', 'foto_menu', 'foto_siswa', 'status_laporan', 'action'])
			->make(true);
	}

	public function create()
	{
		if (!$this->canCreate()) {
			return redirect('forbidden');
		}

		return view('backend.laporansekolah.create', $this->formData());
	}

	public function store(Request $request)
	{
		if (!$this->canCreate()) {
			return redirect('forbidden');
		}

		$validated = $this->validateStore($request);
		$validated = $this->applyContext($validated);

		if ($existing = $this->duplicateLaporan($validated)) {
			return redirect('dashboard/laporan-sekolahs/'.Hashids::encode($existing->id).'/edit')
				->with('error_message', 'Laporan sekolah untuk tanggal, sekolah, SPPG, dan menu tersebut sudah ada. Silakan edit laporan yang sudah tersedia.');
		}

		try {
			DB::transaction(function () use ($request, $validated) {
				$validated['distribusi_id'] = optional($this->matchingDistribusi($validated))->id;
				$validated['foto_menu'] = $this->storeFoto($request, 'foto_menu', 'laporan-sekolah/menu');
				$validated['foto_siswa'] = $this->storeFoto($request, 'foto_siswa', 'laporan-sekolah/siswa');
				$validated['status_laporan'] = 1;
				$validated['waktu_upload'] = now();
				$validated['created_by'] = Auth::id();
				$validated['updated_by'] = Auth::id();

				$laporan = LaporanSekolah::create($validated);
				$this->confirmDistribusi($laporan);
			});

			return redirect('dashboard/laporan-sekolahs')->with('flash_message', 'Laporan sekolah berhasil disimpan');
		} catch (\Exception $e) {
			return redirect()->back()->with('error_message', $e->getMessage())->withInput();
		}
	}

	public function show($id)
	{
		if (!$this->canRead()) {
			return redirect('forbidden');
		}

		$laporan = $this->findByHash($id);
		if (!$this->canViewLaporan($laporan)) {
			return redirect('forbidden');
		}

		return view('backend.laporansekolah.show', compact('laporan'));
	}

	public function edit($id)
	{
		$laporan = $this->findByHash($id);

		if (!$this->canUpdateLaporan($laporan)) {
			return redirect('forbidden');
		}

		return view('backend.laporansekolah.edit', $this->formData($laporan));
	}

	public function update(Request $request, $id)
	{
		$laporan = $this->findByHash($id);

		if (!$this->canUpdateLaporan($laporan)) {
			return redirect('forbidden');
		}

		$validated = $this->validateUpdate($request, $laporan);
		$validated = $this->applyContext($validated, $laporan);

		if ($existing = $this->duplicateLaporan($validated, $laporan->id)) {
			return redirect('dashboard/laporan-sekolahs/'.Hashids::encode($existing->id).'/edit')
				->with('error_message', 'Laporan sekolah untuk tanggal, sekolah, SPPG, dan menu tersebut sudah ada.');
		}

		try {
			DB::transaction(function () use ($request, $laporan, $validated) {
				$validated['distribusi_id'] = optional($this->matchingDistribusi($validated))->id;

				if ($request->hasFile('foto_menu')) {
					$this->deleteFoto($laporan->foto_menu);
					$validated['foto_menu'] = $this->storeFoto($request, 'foto_menu', 'laporan-sekolah/menu');
				} else {
					unset($validated['foto_menu']);
				}

				if ($request->hasFile('foto_siswa')) {
					$this->deleteFoto($laporan->foto_siswa);
					$validated['foto_siswa'] = $this->storeFoto($request, 'foto_siswa', 'laporan-sekolah/siswa');
				} else {
					unset($validated['foto_siswa']);
				}

				$validated['status_laporan'] = 1;
				$validated['verified_by'] = null;
				$validated['verified_at'] = null;
				$validated['updated_by'] = Auth::id();

				$laporan->update($validated);
				$this->confirmDistribusi($laporan);
			});

			return redirect('dashboard/laporan-sekolahs')->with('flash_message', 'Laporan sekolah berhasil diupdate');
		} catch (\Exception $e) {
			return redirect()->back()->with('error_message', $e->getMessage())->withInput();
		}
	}

	public function destroy($id)
	{
		$laporan = $this->findByHash($id);

		if (!$this->canDeleteLaporan($laporan)) {
			return redirect('forbidden');
		}

		try {
			DB::transaction(function () use ($laporan) {
				$this->deleteFoto($laporan->foto_menu);
				$this->deleteFoto($laporan->foto_siswa);
				$laporan->delete();
			});

			return redirect('dashboard/laporan-sekolahs')->with('flash_message', 'Laporan sekolah berhasil dihapus');
		} catch (\Exception $e) {
			return redirect()->back()->with('error_message', $e->getMessage());
		}
	}

	public function deleteAll(Request $request)
	{
		if (!$this->isAdminUser() || !$this->canAccess('delete')) {
			return redirect('forbidden');
		}

		if (!$request->has('id')) {
			return redirect('dashboard/laporan-sekolahs')->with('flash_message', 'Data mu aman, belum dihapus');
		}

		try {
			DB::transaction(function () use ($request) {
				foreach ($request->id as $id) {
					$decoded = Hashids::decode($id);
					$laporan = LaporanSekolah::find($decoded[0] ?? null);

					if ($laporan && $this->canDeleteLaporan($laporan)) {
						$this->deleteFoto($laporan->foto_menu);
						$this->deleteFoto($laporan->foto_siswa);
						$laporan->delete();
					}
				}
			});

			return redirect('dashboard/laporan-sekolahs')->with('flash_message', 'Laporan sekolah berhasil dihapus');
		} catch (\Exception $e) {
			return redirect()->back()->with('error_message', $e->getMessage());
		}
	}

	public function verify(Request $request, $id)
	{
		if (!$this->canVerify()) {
			return redirect('forbidden');
		}

		$laporan = $this->findByHash($id);

		$validated = $request->validate([
			'catatan_verifikasi' => 'nullable|string',
		]);

		$laporan->update([
			'status_laporan' => 2,
			'catatan_verifikasi' => $validated['catatan_verifikasi'] ?? null,
			'verified_by' => Auth::id(),
			'verified_at' => now(),
			'updated_by' => Auth::id(),
		]);

		if ($laporan->distribusi_id) {
			Distribusi::where('id', $laporan->distribusi_id)->update(['status_distribusi' => 2]);
		}

		return redirect()->back()->with('flash_message', 'Laporan sekolah berhasil diverifikasi');
	}

	public function reject(Request $request, $id)
	{
		if (!$this->canVerify()) {
			return redirect('forbidden');
		}

		$laporan = $this->findByHash($id);

		$validated = $request->validate([
			'catatan_verifikasi' => 'required|string',
		]);

		$laporan->update([
			'status_laporan' => 3,
			'catatan_verifikasi' => $validated['catatan_verifikasi'],
			'verified_by' => Auth::id(),
			'verified_at' => now(),
			'updated_by' => Auth::id(),
		]);

		return redirect()->back()->with('flash_message', 'Laporan sekolah ditandai perlu perbaikan');
	}

	private function validateStore(Request $request)
	{
		return $request->validate($this->rules('required'));
	}

	private function validateUpdate(Request $request, LaporanSekolah $laporan)
	{
		return $request->validate($this->rules($laporan->foto_menu ? 'nullable' : 'required', $laporan->foto_siswa ? 'nullable' : 'required'));
	}

	private function rules($fotoMenuRule, $fotoSiswaRule = null)
	{
		$fotoSiswaRule = $fotoSiswaRule ?: $fotoMenuRule;

		return [
			'tanggal' => 'required|date',
			'sppg_id' => 'required',
			'sekolah_id' => 'nullable',
			'menu_harian_id' => 'nullable',
			'foto_menu' => $fotoMenuRule.'|image|mimes:jpg,jpeg,png|max:2048',
			'foto_siswa' => $fotoSiswaRule.'|image|mimes:jpg,jpeg,png|max:2048',
			'latitude' => 'nullable|numeric',
			'longitude' => 'nullable|numeric',
			'lokasi' => 'nullable|string',
			'rating' => 'required|integer|min:1|max:5',
		];
	}

	private function applyContext(array $data, LaporanSekolah $laporan = null)
	{
		if ($this->isSchoolUser() && !$this->isAdminUser()) {
			$data['sekolah_id'] = $this->activeSekolahId();
			$this->ensureSppgServesSekolah($data['sppg_id'], $data['sekolah_id']);
		}

		if ($this->isAdminUser() && empty($data['sekolah_id'])) {
			throw new \Exception('Sekolah wajib dipilih.');
		}

		$this->ensureMenuBelongsToSppg($data['menu_harian_id'] ?? null, $data['sppg_id'] ?? null);

		return $data;
	}

	private function duplicateLaporan(array $data, $ignoreId = null)
	{
		$query = LaporanSekolah::whereDate('tanggal', $data['tanggal'])
			->where('sekolah_id', $data['sekolah_id'])
			->where('sppg_id', $data['sppg_id']);

		if (empty($data['menu_harian_id'])) {
			$query->whereNull('menu_harian_id');
		} else {
			$query->where('menu_harian_id', $data['menu_harian_id']);
		}

		if ($ignoreId) {
			$query->where('id', '!=', $ignoreId);
		}

		return $query->first();
	}

	private function matchingDistribusi(array $data)
	{
		$query = Distribusi::whereDate('tanggal', $data['tanggal'])
			->where('sekolah_id', $data['sekolah_id'])
			->where('sppg_id', $data['sppg_id']);

		if (!empty($data['menu_harian_id'])) {
			$query->where('menu_harian_id', $data['menu_harian_id']);
		}

		return $query->latest('id')->first();
	}

	private function confirmDistribusi(LaporanSekolah $laporan)
	{
		if (!$laporan->distribusi_id) {
			return;
		}

		Distribusi::where('id', $laporan->distribusi_id)
			->where('status_distribusi', '!=', 2)
			->update(['status_distribusi' => 2]);
	}

	private function formData(LaporanSekolah $laporan = null)
	{
		$sekolahs = $this->availableSekolahs();
		$selectedSekolahId = old('sekolah_id', optional($laporan)->sekolah_id ?: $this->activeSekolahId());
		$sppgs = $this->availableSppgs($selectedSekolahId);

		return [
			'laporan' => $laporan,
			'sekolahs' => $sekolahs,
			'sppgs' => $sppgs,
			'menuHarians' => $this->availableMenuHarians(old('sppg_id', optional($laporan)->sppg_id)),
			'isAdminLaporanSekolah' => $this->isAdminUser(),
			'isSekolahLaporanSekolah' => $this->isSchoolUser() && !$this->isAdminUser(),
		];
	}

	private function availableSekolahs()
	{
		if ($this->isAdminUser()) {
			return Sekolah::orderBy('nama')->pluck('nama', 'id');
		}

		if ($this->activeSekolahId()) {
			return Sekolah::where('id', $this->activeSekolahId())->orderBy('nama')->pluck('nama', 'id');
		}

		return collect();
	}

	private function availableSppgs($sekolahId = null)
	{
		if ($this->isAdminUser()) {
			return Sppg::orderBy('nama')->pluck('nama', 'id');
		}

		$sekolah = Sekolah::with('sppgs')->find($sekolahId ?: $this->activeSekolahId());

		return $sekolah ? $sekolah->sppgs()->orderBy('nama')->get()->pluck('nama', 'id') : collect();
	}

	private function availableMenuHarians($sppgId = null)
	{
		$query = MenuHarian::query();

		if ($sppgId) {
			$query->where('sppg_id', $sppgId);
		} elseif (!$this->isAdminUser()) {
			$query->whereIn('sppg_id', $this->availableSppgs()->keys()->all());
		}

		return $query->orderBy('tanggal', 'desc')->orderBy('nama')->get()
			->mapWithKeys(fn($menu) => [$menu->id => $menu->nama.' - '.optional($menu->tanggal)->format('d-m-Y')]);
	}

	private function applyFilters($query, Request $request)
	{
		if ($request->filled('tanggal')) {
			$query->whereDate('tanggal', $request->tanggal);
		}

		if ($request->filled('status_laporan')) {
			$query->where('status_laporan', $request->status_laporan);
		}

		return $query;
	}

	private function visibleLaporans($query)
	{
		if ($this->isAdminUser()) {
			return $query;
		}

		if ($this->activeSppgId()) {
			return $query->where('sppg_id', $this->activeSppgId());
		}

		if ($this->activeSekolahId()) {
			return $query->where('sekolah_id', $this->activeSekolahId());
		}

		return $query->whereRaw('1 = 0');
	}

	private function findByHash($hash)
	{
		$ids = Hashids::decode($hash);

		return LaporanSekolah::with(['sekolah', 'sppg', 'menuHarian', 'distribusi', 'verifiedBy', 'createdBy', 'updatedBy'])->findOrFail($ids[0] ?? null);
	}

	private function storeFoto(Request $request, $field, $directory)
	{
		$file = $request->file($field);
		$destination = $this->uploadPath($directory);
		$filename = time().'_'.uniqid().'_'.str_replace(' ', '-', $file->getClientOriginalName());

		if (!File::isDirectory($destination)) {
			File::makeDirectory($destination, 0777, true, true);
		}

		$file->move($destination, $filename);

		return $directory.'/'.$filename;
	}

	private function deleteFoto($path)
	{
		if (!$path) {
			return;
		}

		$uploadPath = $this->uploadPath($path);
		if (File::exists($uploadPath)) {
			File::delete($uploadPath);
			return;
		}

		if (Storage::disk('public')->exists($path)) {
			Storage::disk('public')->delete($path);
		}
	}

	private function thumbnail($path)
	{
		if (!$path) {
			return '-';
		}

		return '<a href="'.asset('po-content/uploads/'.$path).'" target="_blank"><img src="'.asset('po-content/uploads/'.$path).'" class="img-fluid rounded" style="width:60px;height:45px;object-fit:cover;" alt=""></a>';
	}

	private function ratingStars($rating)
	{
		$rating = (int) $rating;

		if (!$rating) {
			return '-';
		}

		$stars = '';
		for ($i = 1; $i <= 5; $i++) {
			$stars .= $i <= $rating ? '&#9733;' : '&#9734;';
		}

		return '<span class="text-warning" title="'.$rating.'/5">'.$stars.'</span><span class="text-muted ml-1">('.$rating.'/5)</span>';
	}

	private function uploadPath($path = '')
	{
		return str_replace(['\po-includes','/po-includes'], '', base_path('po-content/uploads/'.$path));
	}

	private function statusBadge($status)
	{
		$map = [
			1 => ['Sudah Lapor', 'badge-info'],
			2 => ['Terverifikasi', 'badge-success'],
			3 => ['Perlu Perbaikan / Ditolak', 'badge-danger'],
		];

		$item = $map[(int) $status] ?? ['Belum Lapor', 'badge-light'];

		return '<span class="badge '.$item[1].'">'.$item[0].'</span>';
	}

	private function ensureSppgServesSekolah($sppgId, $sekolahId)
	{
		if (!$sppgId || !$sekolahId || !Sekolah::where('id', $sekolahId)->whereHas('sppgs', fn($q) => $q->where('sppgs.id', $sppgId))->exists()) {
			abort(403, 'SPPG tidak terhubung dengan sekolah tersebut');
		}
	}

	private function ensureMenuBelongsToSppg($menuHarianId, $sppgId)
	{
		if (!$menuHarianId) {
			return;
		}

		if (!$sppgId || !MenuHarian::where('id', $menuHarianId)->where('sppg_id', $sppgId)->exists()) {
			abort(403, 'Menu harian tidak sesuai dengan SPPG tersebut');
		}
	}

	private function canRead()
	{
		return $this->canAccess('read') && ($this->isAdminUser() || $this->activeSppgId() || $this->activeSekolahId());
	}

	private function canCreate()
	{
		return $this->canAccess('create') && ($this->isAdminUser() || $this->activeSekolahId());
	}

	private function canViewLaporan(LaporanSekolah $laporan)
	{
		if ($this->isAdminUser()) {
			return true;
		}

		if ($this->activeSppgId()) {
			return (int) $laporan->sppg_id === (int) $this->activeSppgId();
		}

		return $this->activeSekolahId() && (int) $laporan->sekolah_id === (int) $this->activeSekolahId();
	}

	private function canUpdateLaporan(LaporanSekolah $laporan)
	{
		if (!$this->canAccess('update')) {
			return false;
		}

		if ($this->isAdminUser()) {
			return true;
		}

		return $this->activeSekolahId() && (int) $laporan->sekolah_id === (int) $this->activeSekolahId();
	}

	private function canVerify()
	{
		return $this->isAdminUser() && $this->canAccess('verify');
	}

	private function canDeleteLaporan(LaporanSekolah $laporan)
	{
		return $this->isAdminUser() && $this->canAccess('delete');
	}

	private function canAccess($action)
	{
		return Auth::user()->can($action.'-laporan-sekolahs');
	}

	private function isAdminUser()
	{
		return Auth::user()->hasRole('superadmin')
			|| Auth::user()->hasRole('superadmin 2')
			|| Auth::user()->hasRole('admin');
	}

	private function isSchoolUser()
	{
		return Auth::user()->hasRole('sekolah') || $this->activeSekolahId();
	}

	private function activeSppgId()
	{
		return Auth::user()->sppg_id;
	}

	private function activeSekolahId()
	{
		return Auth::user()->sekolah_id;
	}
}
