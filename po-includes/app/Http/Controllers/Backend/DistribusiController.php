<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

use App\Distribusi;
use App\MenuHarian;
use App\Sekolah;
use App\Sppg;

use Yajra\Datatables\Datatables;
use Vinkla\Hashids\Facades\Hashids;

class DistribusiController extends Controller
{
	public function index(Request $request)
	{
		if (!$this->canRead()) {
			return redirect('forbidden');
		}

		return view('backend.distribusi.datatable', [
			'summary' => $this->summary($request),
			'sppgs' => $this->availableSppgs()->pluck('nama', 'id'),
			'sekolahs' => $this->availableSekolahs()->pluck('nama', 'id'),
		]);
	}

	public function getIndex(Request $request)
	{
		return $this->index($request);
	}

	public function anyData(Request $request)
	{
		if (!$this->canRead()) {
			abort(403);
		}

		$distribusis = $this->applyFilters(
			$this->visibleDistribusis(Distribusi::with(['sppg', 'sekolah', 'menuHarian'])),
			$request
		)->orderBy('tanggal', 'desc')->orderBy('id', 'desc');

		return Datatables::of($distribusis)
			->addColumn('check', function ($distribusi) {
				return '<div style="text-align:center;">
					<input type="checkbox" />
					<input type="hidden" class="deldata" name="id[]" value="'.Hashids::encode($distribusi->id).'" disabled />
				</div>';
			})
			->addColumn('tanggal', fn($s) => optional($s->tanggal)->format('d-m-Y') ?: '-')
			->addColumn('sppg', fn($s) => optional($s->sppg)->nama ?? '-')
			->addColumn('sekolah', fn($s) => optional($s->sekolah)->nama ?? '-')
			->addColumn('menu_harian', fn($s) => optional($s->menuHarian)->nama ?? '-')
			->addColumn('jumlah_porsi', fn($s) => number_format($s->jumlah_porsi, 0, ',', '.'))
			->addColumn('status_distribusi', fn($s) => $this->statusBadge($s->status_distribusi))
			->addColumn('action', function ($distribusi) {
				$hash = Hashids::encode($distribusi->id);
				$btn = '<div style="text-align:center;"><div class="btn-group">';
				$btn .= '<a href="'.url('dashboard/distribusis/'.$hash).'" class="btn btn-info btn-xs btn-icon" title="'.__('general.show').'" data-toggle="tooltip" data-placement="left"><i class="fa fa-eye"></i></a>';

				if ($this->canUpdateDistribusi($distribusi)) {
					$btn .= '<a href="'.url('dashboard/distribusis/'.$hash.'/edit').'" class="btn btn-primary btn-xs btn-icon" title="'.__('general.edit').'" data-toggle="tooltip" data-placement="left"><i class="fa fa-edit"></i></a>';
				}

				if ($this->canDeleteDistribusi($distribusi)) {
					$btn .= '<a href="'.url('dashboard/distribusis/'.$hash).'" class="btn btn-danger btn-xs btn-icon" data-delete="" title="'.__('general.delete').'" data-toggle="tooltip" data-placement="left"><i class="fa fa-trash"></i></a>';
				}

				$btn .= '</div></div>';

				return $btn;
			})
			->escapeColumns([])
			->rawColumns(['check', 'status_distribusi', 'action'])
			->make(true);
	}

	public function create()
	{
		if (!$this->canCreate()) {
			return redirect('forbidden');
		}

		return view('backend.distribusi.create', $this->formData());
	}

	public function store(Request $request)
	{
		if (!$this->canCreate()) {
			return redirect('forbidden');
		}

		$validated = $this->validateStore($request);
		$validated = $this->applyStoreContext($request, $validated);

		if ($message = $this->duplicateMessage($validated)) {
			return redirect()->back()->with('error_message', $message)->withInput();
		}

		try {
			DB::transaction(function () use ($request, $validated) {
				$validated['created_by'] = Auth::id();
				$validated['updated_by'] = Auth::id();

				Distribusi::create($validated);
			});

			return redirect('dashboard/distribusis')->with('flash_message', 'Data distribusi berhasil disimpan');
		} catch (\Exception $e) {
			return redirect()->back()->with('error_message', $e->getMessage())->withInput();
		}
	}

	public function show($id)
	{
		if (!$this->canRead()) {
			return redirect('forbidden');
		}

		$distribusi = $this->findByHash($id);

		if (!$this->canViewDistribusi($distribusi)) {
			return redirect('forbidden');
		}

		return view('backend.distribusi.show', compact('distribusi'));
	}

	public function edit($id)
	{
		if (!$this->canAccess('update')) {
			return redirect('forbidden');
		}

		$distribusi = $this->findByHash($id);

		if (!$this->canUpdateDistribusi($distribusi)) {
			return redirect('forbidden');
		}

		return view('backend.distribusi.edit', $this->formData($distribusi));
	}

	public function update(Request $request, $id)
	{
		if (!$this->canAccess('update')) {
			return redirect('forbidden');
		}

		$distribusi = $this->findByHash($id);

		if (!$this->canUpdateDistribusi($distribusi)) {
			return redirect('forbidden');
		}

		$validated = $this->validateUpdate($request, $distribusi);
		$validated = $this->applyUpdateContext($request, $distribusi, $validated);

		if (!$this->isSchoolUser() && ($message = $this->duplicateMessage($validated, $distribusi->id))) {
			return redirect()->back()->with('error_message', $message)->withInput();
		}

		try {
			DB::transaction(function () use ($request, $distribusi, $validated) {
				$validated['updated_by'] = Auth::id();
				$distribusi->update($validated);
			});

			return redirect('dashboard/distribusis')->with('flash_message', 'Data distribusi berhasil diupdate');
		} catch (\Exception $e) {
			return redirect()->back()->with('error_message', $e->getMessage())->withInput();
		}
	}

	public function destroy($id)
	{
		if (!$this->canAccess('delete')) {
			return redirect('forbidden');
		}

		$distribusi = $this->findByHash($id);

		if (!$this->canDeleteDistribusi($distribusi)) {
			return redirect('forbidden');
		}

		try {
			DB::transaction(function () use ($distribusi) {
				$distribusi->delete();
			});

			return redirect('dashboard/distribusis')->with('flash_message', 'Data distribusi berhasil dihapus');
		} catch (\Exception $e) {
			return redirect()->back()->with('error_message', $e->getMessage());
		}
	}

	public function deleteAll(Request $request)
	{
		if (!$this->canAccess('delete') || !$this->isAdminUser()) {
			return redirect('forbidden');
		}

		if (!$request->has('id')) {
			return redirect('dashboard/distribusis')->with('flash_message', 'Data mu aman, belum dihapus');
		}

		try {
			DB::transaction(function () use ($request) {
				foreach ($request->id as $id) {
					$decoded = Hashids::decode($id);
					$distribusi = Distribusi::find($decoded[0] ?? null);

					if ($distribusi && $this->canDeleteDistribusi($distribusi)) {
						$distribusi->delete();
					}
				}
			});

			return redirect('dashboard/distribusis')->with('flash_message', 'Data distribusi berhasil dihapus');
		} catch (\Exception $e) {
			return redirect()->back()->with('error_message', $e->getMessage());
		}
	}

	private function validateStore(Request $request)
	{
		$rules = $this->baseRules();

		if ($this->isAdminUser()) {
			$rules['sppg_id'] = 'required';
			$rules['sekolah_id'] = 'required';
		} elseif ($this->isSppgUser()) {
			$rules['sekolah_id'] = 'required';
		}

		return $request->validate($rules);
	}

	private function validateUpdate(Request $request, Distribusi $distribusi)
	{
		return $request->validate($this->baseRules());
	}

	private function baseRules()
	{
		return [
			'tanggal' => 'required|date',
			'sppg_id' => 'nullable',
			'sekolah_id' => 'nullable',
			'menu_harian_id' => 'nullable',
			'jumlah_porsi' => 'required|numeric|min:0',
			'keterangan' => 'nullable|string',
		];
	}

	private function applyStoreContext(Request $request, array $data)
	{
		if ($this->isAdminUser()) {
			$this->ensureMenuBelongsToSppg($data['menu_harian_id'] ?? null, $data['sppg_id'] ?? null);
			$data['status_distribusi'] = 1;
			return $data;
		}

		if ($this->isSppgUser()) {
			$data['sppg_id'] = $this->activeSppgId();
			$data['status_distribusi'] = 1;
			$this->ensureSekolahServedBySppg($data['sekolah_id'] ?? null, $data['sppg_id']);
			$this->ensureMenuBelongsToSppg($data['menu_harian_id'] ?? null, $data['sppg_id']);
			return $data;
		}

		return $data;
	}

	private function applyUpdateContext(Request $request, Distribusi $distribusi, array $data)
	{
		$data['status_distribusi'] = (int) $distribusi->status_distribusi > 1 ? 2 : 1;

		if ($this->isSppgUser() && !$this->isAdminUser()) {
			$data['sppg_id'] = $this->activeSppgId();
			$this->ensureSekolahServedBySppg($data['sekolah_id'] ?? null, $data['sppg_id']);
			$this->ensureMenuBelongsToSppg($data['menu_harian_id'] ?? null, $data['sppg_id']);
			return $data;
		}

		$this->ensureMenuBelongsToSppg($data['menu_harian_id'] ?? null, $data['sppg_id'] ?? null);

		return $data;
	}

	private function duplicateMessage(array $data, $ignoreId = null)
	{
		$query = Distribusi::whereDate('tanggal', $data['tanggal'])
			->where('sppg_id', $data['sppg_id'])
			->where('sekolah_id', $data['sekolah_id']);

		if (empty($data['menu_harian_id'])) {
			$query->whereNull('menu_harian_id');
		} else {
			$query->where('menu_harian_id', $data['menu_harian_id']);
		}

		if ($ignoreId) {
			$query->where('id', '!=', $ignoreId);
		}

		return $query->exists()
			? 'Data distribusi untuk tanggal, SPPG, sekolah, dan menu tersebut sudah ada. Silakan update data yang sudah tersedia.'
			: null;
	}

	private function formData(Distribusi $distribusi = null)
	{
		return [
			'distribusi' => $distribusi,
			'sppgs' => $this->availableSppgs($distribusi)->pluck('nama', 'id'),
			'sekolahs' => $this->availableSekolahs($distribusi)->pluck('nama', 'id'),
			'menuHarians' => $this->availableMenuHarians($distribusi)->mapWithKeys(function ($menu) {
				return [$menu->id => $menu->nama.' - '.optional($menu->tanggal)->format('d-m-Y')];
			}),
			'isAdminDistribusi' => $this->isAdminUser(),
			'isSppgDistribusi' => $this->isSppgUser() && !$this->isAdminUser(),
			'isSekolahDistribusi' => $this->isSchoolUser() && !$this->isAdminUser() && !$this->isSppgUser(),
		];
	}

	private function availableSppgs(Distribusi $distribusi = null)
	{
		if ($this->isAdminUser()) {
			return Sppg::orderBy('nama')->get();
		}

		if ($this->isSppgUser()) {
			return Sppg::where('id', $this->activeSppgId())->orderBy('nama')->get();
		}

		$sekolah = Sekolah::with('sppgs')->find($this->activeSekolahId());

		return $sekolah ? $sekolah->sppgs()->orderBy('nama')->get() : collect();
	}

	private function availableSekolahs(Distribusi $distribusi = null)
	{
		if ($this->isAdminUser()) {
			return Sekolah::orderBy('nama')->get();
		}

		if ($this->isSppgUser()) {
			$sppg = Sppg::find($this->activeSppgId());
			return $sppg ? $sppg->sekolahs()->orderBy('nama')->get() : collect();
		}

		return Sekolah::where('id', $this->activeSekolahId())->orderBy('nama')->get();
	}

	private function availableMenuHarians(Distribusi $distribusi = null)
	{
		if ($this->isAdminUser()) {
			return MenuHarian::orderBy('tanggal', 'desc')->orderBy('nama')->get();
		}

		if ($this->isSppgUser()) {
			$query = MenuHarian::where('sppg_id', $this->activeSppgId());

			if (!$distribusi) {
				$query->whereDate('tanggal', date('Y-m-d'));
			}

			return $query->orderBy('tanggal', 'desc')->orderBy('nama')->get();
		}

		$sppgIds = $this->availableSppgs($distribusi)->pluck('id')->all();

		return MenuHarian::whereIn('sppg_id', $sppgIds)->orderBy('tanggal', 'desc')->orderBy('nama')->get();
	}

	private function summary(Request $request)
	{
		$query = $this->applyFilters($this->visibleDistribusis(Distribusi::query()), $request);
		$total = (clone $query)->count();

		if (!Schema::hasTable('laporan_sekolahs')) {
			return [
				'total' => $total,
				'sudah_lapor' => (clone $query)->where('status_distribusi', '>', 1)->count(),
				'belum_lapor' => (clone $query)->where('status_distribusi', 1)->count(),
			];
		}

		$sudahLapor = (clone $query)->where('status_distribusi', '>', 1)->count();

		return [
			'total' => $total,
			'sudah_lapor' => $sudahLapor,
			'belum_lapor' => (clone $query)->where('status_distribusi', 1)->count(),
		];
	}

	private function applyFilters($query, Request $request)
	{
		if ($request->filled('tanggal')) {
			$query->whereDate('tanggal', $request->tanggal);
		}

		if ($request->filled('sppg_id')) {
			$query->where('sppg_id', $request->sppg_id);
		}

		if ($request->filled('sekolah_id')) {
			$query->where('sekolah_id', $request->sekolah_id);
		}

		if ($request->filled('status_distribusi')) {
			if ((int) $request->status_distribusi === 2) {
				$query->where('status_distribusi', '>', 1);
			} elseif ((int) $request->status_distribusi === 1) {
				$query->where('status_distribusi', 1);
			}
		}

		return $query;
	}

	private function visibleDistribusis($query)
	{
		if ($this->isAdminUser()) {
			return $query;
		}

		if ($this->isSppgUser()) {
			return $query->where('sppg_id', $this->activeSppgId());
		}

		if ($this->isSchoolUser()) {
			return $query->where('sekolah_id', $this->activeSekolahId());
		}

		return $query->whereRaw('1 = 0');
	}

	private function canViewDistribusi(Distribusi $distribusi)
	{
		if ($this->isAdminUser()) {
			return true;
		}

		if ($this->isSppgUser()) {
			return (int) $distribusi->sppg_id === (int) $this->activeSppgId();
		}

		if ($this->isSchoolUser()) {
			return (int) $distribusi->sekolah_id === (int) $this->activeSekolahId();
		}

		return false;
	}

	private function canUpdateDistribusi(Distribusi $distribusi)
	{
		if (!$this->canAccess('update')) {
			return false;
		}

		if ($this->isAdminUser()) {
			return true;
		}

		return !$this->isSchoolUser() && $this->canViewDistribusi($distribusi);
	}

	private function canDeleteDistribusi(Distribusi $distribusi)
	{
		return $this->canAccess('delete') && $this->isAdminUser();
	}

	private function canRead()
	{
		return $this->canAccess('read') && ($this->isAdminUser() || $this->isSppgUser() || $this->isSchoolUser());
	}

	private function canCreate()
	{
		return $this->canAccess('create') && ($this->isAdminUser() || $this->isSppgUser());
	}

	private function canAccess($action)
	{
		return Auth::user()->can($action.'-distribusis');
	}

	private function findByHash($hash)
	{
		$ids = Hashids::decode($hash);

		return Distribusi::with(['sppg', 'sekolah', 'menuHarian', 'createdBy', 'updatedBy'])->findOrFail($ids[0] ?? null);
	}

	private function ensureSekolahServedBySppg($sekolahId, $sppgId)
	{
		if (!$sekolahId || !$sppgId || !Sppg::where('id', $sppgId)->whereHas('sekolahs', fn($q) => $q->where('sekolahs.id', $sekolahId))->exists()) {
			abort(403, 'Sekolah tidak terhubung dengan SPPG tersebut');
		}
	}

	private function ensureSppgServesSekolah($sppgId, $sekolahId)
	{
		if (!$sppgId || !$sekolahId || !Sekolah::where('id', $sekolahId)->whereHas('sppgs', fn($q) => $q->where('sppgs.id', $sppgId))->exists()) {
			abort(403, 'SPPG tidak terhubung dengan sekolah tersebut');
		}
	}

	private function defaultSppgForSekolah($sekolahId)
	{
		$sekolah = Sekolah::find($sekolahId);

		if (!$sekolah) {
			abort(403, 'Sekolah tidak ditemukan');
		}

		$sppgId = $sekolah->sppgs()->orderBy('sppgs.id')->value('sppgs.id');

		if (!$sppgId) {
			abort(403, 'Sekolah belum terhubung dengan SPPG');
		}

		return $sppgId;
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

	private function statusBadge($status)
	{
		$map = [
			1 => ['Belum Lapor Sekolah', 'badge-warning'],
			2 => ['Sudah Lapor Sekolah', 'badge-info'],
		];

		$item = (int) $status > 1
			? $map[2]
			: ($map[(int) $status] ?? ['Belum Lapor Sekolah', 'badge-warning']);

		return '<span class="badge '.$item[1].'">'.$item[0].'</span>';
	}

	private function isAdminUser()
	{
		return Auth::user()->hasRole('superadmin')
			|| Auth::user()->hasRole('superadmin 2')
			|| Auth::user()->hasRole('admin');
	}

	private function isSppgUser()
	{
		return Auth::user()->hasRole('sppg') && $this->activeSppgId();
	}

	private function isSchoolUser()
	{
		return Auth::user()->hasRole('sekolah') && $this->activeSekolahId();
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
