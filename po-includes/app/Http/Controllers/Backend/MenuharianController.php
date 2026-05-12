<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\MenuHarian;
use App\Sppg;
use App\Sekolah;
use App\Distribusi;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Yajra\Datatables\Datatables;
use Vinkla\Hashids\Facades\Hashids;

class MenuharianController extends Controller
{
    public function index(Request $request)
    {
		if($this->canList()) {
			return view('backend.menuharian.datatable');
		}

		return redirect('forbidden');
    }

	public function getIndex()
	{
		if($this->canList()) {
			return view('backend.menuharian.datatable');
		}

		return redirect('forbidden');
	}

	public function anyData()
	{
		if (!$this->canList()) {
			abort(403);
		}

		$menuharians = $this->visibleMenuHarians(MenuHarian::with('sppg'))
			->orderBy('id', 'desc');

		return Datatables::of($menuharians)
			->addColumn('check', function ($menuharian) {
				return '<div style="text-align:center;">
					<input type="checkbox" />
					<input type="hidden" class="deldata" name="id[]" value="'.Hashids::encode($menuharian->id).'" disabled />
				</div>';
			})
			->addColumn('tanggal', fn($s) => optional($s->tanggal)->format('d-m-Y') ?: '-')
			->addColumn('foto', function ($s) {
				if (!$s->foto) return '-';

				return '<img src="'.$this->fotoUrl($s->foto).'" class="img-fluid rounded" style="width:60px;height:45px;object-fit:cover;" alt="">';
			})
			->addColumn('nama', fn($s) => $s->nama)
			->addColumn('sppg', fn($s) => optional($s->sppg)->nama ?? '-')
			->addColumn('gizi_kecil', fn($s) => $this->nutritionSummary($s, 'kecil'))
			->addColumn('gizi_besar', fn($s) => $this->nutritionSummary($s, 'besar'))
            ->addColumn('action', function ($menuharian) {
				$btn = '<div style="text-align:center;"><div class="btn-group">';
				$btn .= '<a href="'.url('dashboard/menu-harians/'.Hashids::encode($menuharian->id)).'" class="btn btn-info btn-xs btn-icon" title="'.__('general.show').'" data-toggle="tooltip" data-placement="left"><i class="fa fa-eye"></i></a>';
				if ($this->canAccess('update') && $this->canModifyMenu($menuharian)) {
					$btn .= '<a href="'.url('dashboard/menu-harians/'.Hashids::encode($menuharian->id).'/edit').'" class="btn btn-primary btn-xs btn-icon" title="'.__('general.edit').'" data-toggle="tooltip" data-placement="left"><i class="fa fa-edit"></i></a>';
				}
				if ($this->canAccess('delete') && $this->canModifyMenu($menuharian)) {
					$btn .= '<a href="'.url('dashboard/menu-harians/'.Hashids::encode($menuharian->id).'').'" class="btn btn-danger btn-xs btn-icon" data-delete="" title="'.__('general.delete').'" data-toggle="tooltip" data-placement="left"><i class="fa fa-trash"></i></a>';
				}
				$btn .= '</div></div>';
				return $btn;
            })
			->addColumn('control', function ($menuharian) {
				return '<div style="text-align:center;"><a href="javascript:void(0);" class="btn btn-secondary btn-xs btn-icon" data-placement="left"><i class="fa fa-plus"></i></a></div>';
			})
			->escapeColumns([])
			->rawColumns(['foto', 'action'])
			->make(true);
	}

	public function create()
    {
		if($this->canCreateMenu()) {
			$sppgs = Sppg::orderBy('nama')->pluck('nama', 'id');
			$distribusiSekolahs = $this->availableDistribusiSekolahs($this->activeSppgId());

			return view('backend.menuharian.create', [
				'menuharian' => null,
				'sppgs' => $sppgs,
				'distribusiSekolahs' => $distribusiSekolahs,
				'distribusis' => collect(),
			]);
		}

		return redirect('forbidden');
    }

	public function store(Request $request)
	{
		if (!$this->canCreateMenu()) {
			return redirect('forbidden');
		}

		$validated = $this->validateMenuHarian($request);
		$validated['sppg_id'] = $this->resolveInputSppgId($request);

		try {
			DB::transaction(function () use ($request, $validated) {
				$validated['foto'] = $this->storeFoto($request);
				$validated['created_by'] = Auth::id();
				$validated['updated_by'] = Auth::id();

				$menuharian = MenuHarian::create($validated);
				$this->syncDistribusiSekolah($request, $menuharian);
			});

			return redirect('dashboard/menu-harians')
				->with('flash_message', 'Menu harian berhasil disimpan');

		} catch (\Exception $e) {
			return redirect()->back()
				->with('error_message', $e->getMessage())
				->withInput();
		}
	}

    public function show($id)
    {
		if($this->canAccess('read')) {
			$ids = Hashids::decode($id);
			$menuharian = MenuHarian::with(['sppg', 'createdBy', 'updatedBy', 'distribusis.sekolah'])->findOrFail($ids[0]);

			if (!$this->canViewMenu($menuharian)) {
				return redirect('forbidden');
			}

			$distribusiSekolahs = $this->availableDistribusiSekolahs($menuharian->sppg_id);
			$distribusis = $menuharian->distribusis->keyBy('sekolah_id');

			return view('backend.menuharian.show', compact('menuharian', 'distribusiSekolahs', 'distribusis'));
		}

		return redirect('forbidden');
    }

    public function edit($id)
	{
		if (!$this->canAccess('update')) {
			return redirect('forbidden')
				->with('error_message', 'Tidak punya akses');
		}

		$decoded = Hashids::decode($id);
		$id = $decoded[0] ?? null;

		if (!$id) {
			return redirect()->back()->with('error_message', 'ID tidak valid');
		}

		$menuharian = MenuHarian::with('distribusis')->findOrFail($id);

		if (!$this->canModifyMenu($menuharian)) {
			return redirect('forbidden')
				->with('error_message', 'Tidak punya akses');
		}

		$sppgs = Sppg::orderBy('nama')->pluck('nama', 'id');
		$distribusiSekolahs = $this->availableDistribusiSekolahs($menuharian->sppg_id);
		$distribusis = $menuharian->distribusis->keyBy('sekolah_id');

		return view('backend.menuharian.edit', compact('menuharian', 'sppgs', 'distribusiSekolahs', 'distribusis'));
	}

	public function update(Request $request, $id)
	{
		if (!$this->canAccess('update')) {
			return redirect('forbidden')
				->with('error_message', 'Tidak punya akses');
		}

		$decoded = Hashids::decode($id);
		$id = $decoded[0] ?? null;

		if (!$id) {
			return redirect()->back()
				->with('error_message', 'ID tidak valid');
		}

		$menuharian = MenuHarian::findOrFail($id);

		if (!$this->canModifyMenu($menuharian)) {
			return redirect('forbidden')
				->with('error_message', 'Tidak punya akses');
		}

		$validated = $this->validateMenuHarian($request);
		$validated['sppg_id'] = $this->resolveInputSppgId($request, $menuharian);

		try {
			DB::transaction(function () use ($request, $menuharian, $validated) {
				if ($request->hasFile('foto')) {
					$this->deleteFoto($menuharian);
					$validated['foto'] = $this->storeFoto($request);
				} else {
					unset($validated['foto']);
				}

				$validated['updated_by'] = Auth::id();
				$menuharian->update($validated);
				$this->syncDistribusiSekolah($request, $menuharian);
			});

			return redirect('dashboard/menu-harians')
				->with('flash_message', 'Menu harian berhasil diupdate');

		} catch (\Exception $e) {
			return redirect()->back()
				->with('error_message', $e->getMessage())
				->withInput();
		}
	}

    public function destroy($id)
    {
		if(!$this->canAccess('delete')) {
			return redirect('forbidden');
		}

		try {
			$ids = Hashids::decode($id);
			$menuharian = MenuHarian::findOrFail($ids[0]);

			if (!$this->canModifyMenu($menuharian)) {
				return redirect('forbidden');
			}

			DB::transaction(function () use ($menuharian) {
				$this->deleteFoto($menuharian);
				$menuharian->delete();
			});

			return redirect('dashboard/menu-harians')->with('flash_message', 'Data berhasil dihapus');
		} catch (\Exception $e) {
			return redirect()->back()->with('error_message', $e->getMessage());
		}
    }

	public function distribusi($id)
	{
		return $this->show($id);
	}

	public function saveDistribusi(Request $request, $id)
	{
		if (!$this->canAccess('update')) {
			return redirect('forbidden');
		}

		$decoded = Hashids::decode($id);
		$menuharian = MenuHarian::findOrFail($decoded[0] ?? null);

		if (!$this->canModifyMenu($menuharian)) {
			return redirect('forbidden');
		}

		try {
			DB::transaction(function () use ($request, $menuharian) {
				$this->syncDistribusiSekolah($request, $menuharian);
			});

			return redirect('dashboard/menu-harians/'.$id)
				->with('flash_message', 'Distribusi ke sekolah berhasil disimpan');
		} catch (\Exception $e) {
			return redirect()->back()
				->with('error_message', $e->getMessage())
				->withInput();
		}
	}

    public function deleteAll(Request $request)
    {
		if(!$this->canAccess('delete')) {
			return redirect('forbidden');
		}

		if (!$request->has('id')) {
			return redirect('dashboard/menu-harians')->with('flash_message', 'Data mu aman, belum dihapus');
		}

		try {
			DB::transaction(function () use ($request) {
				foreach($request->id as $id){
					$idd = Hashids::decode($id);
					$menuharian = MenuHarian::find($idd[0] ?? null);

					if ($menuharian && $this->canModifyMenu($menuharian)) {
						$this->deleteFoto($menuharian);
						$menuharian->delete();
					}
				}
			});

			return redirect('dashboard/menu-harians')->with('flash_message', 'Data berhasil dihapus');
		} catch (\Exception $e) {
			return redirect()->back()->with('error_message', $e->getMessage());
		}
    }

	private function validateMenuHarian(Request $request)
	{
		return $request->validate([
			'tanggal' => 'required|date',
			'nama' => 'required|string|max:255',
			'sppg_id' => 'nullable',
			'deskripsi' => 'nullable|string',
			'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
			'kecil_energi' => 'nullable|numeric|min:0',
			'kecil_lemak' => 'nullable|numeric|min:0',
			'kecil_protein' => 'nullable|numeric|min:0',
			'kecil_karbohidrat' => 'nullable|numeric|min:0',
			'kecil_serat' => 'nullable|numeric|min:0',
			'besar_energi' => 'nullable|numeric|min:0',
			'besar_lemak' => 'nullable|numeric|min:0',
			'besar_protein' => 'nullable|numeric|min:0',
			'besar_karbohidrat' => 'nullable|numeric|min:0',
			'besar_serat' => 'nullable|numeric|min:0',
		]);
	}

	private function syncDistribusiSekolah(Request $request, MenuHarian $menuharian)
	{
		if (!$request->has('distribusi_sekolah_ids')) {
			return;
		}

		$request->validate([
			'distribusi_sekolah_ids' => 'nullable|array',
			'distribusi_sekolah_ids.*' => 'nullable|integer',
			'distribusi_porsi' => 'nullable|array',
			'distribusi_porsi.*' => 'nullable|numeric|min:0',
			'distribusi_keterangan' => 'nullable|array',
			'distribusi_keterangan.*' => 'nullable|string',
		]);

		$sppgId = $menuharian->sppg_id ?: $this->activeSppgId();
		if (!$sppgId) {
			throw new \Exception('SPPG wajib dipilih sebelum membuat distribusi ke sekolah.');
		}

		$allowedSekolahs = $this->availableDistribusiSekolahs($sppgId)->keyBy('id');
		$selectedIds = collect($request->input('distribusi_sekolah_ids', []))
			->filter()
			->map(fn($id) => (int) $id)
			->unique()
			->values();

		if (!$this->isAdminUser()) {
			$invalid = $selectedIds->diff($allowedSekolahs->keys());
			if ($invalid->isNotEmpty()) {
				throw new \Exception('Ada sekolah yang tidak terhubung dengan SPPG Anda.');
			}
		}

		$porsiInput = $request->input('distribusi_porsi', []);
		$keteranganInput = $request->input('distribusi_keterangan', []);

		foreach ($selectedIds as $sekolahId) {
			$sekolah = $allowedSekolahs->get($sekolahId) ?: Sekolah::find($sekolahId);
			if (!$sekolah) {
				continue;
			}

			$existing = Distribusi::where('menu_harian_id', $menuharian->id)
				->where('sekolah_id', $sekolahId)
				->first();

			$statusDistribusi = $existing && (int) $existing->status_distribusi > 1 ? 2 : 1;

			Distribusi::updateOrCreate(
				[
					'menu_harian_id' => $menuharian->id,
					'sekolah_id' => $sekolahId,
				],
				[
					'tanggal' => $menuharian->tanggal,
					'sppg_id' => $sppgId,
					'jumlah_porsi' => isset($porsiInput[$sekolahId]) ? (int) $porsiInput[$sekolahId] : (int) $sekolah->jumlah_total,
					'status_distribusi' => $statusDistribusi,
					'keterangan' => $keteranganInput[$sekolahId] ?? null,
					'created_by' => optional($existing)->created_by ?: Auth::id(),
					'updated_by' => Auth::id(),
				]
			);
		}

		Distribusi::where('menu_harian_id', $menuharian->id)
			->whereNotIn('sekolah_id', $selectedIds->all())
			->where('status_distribusi', 1)
			->delete();
	}

	private function storeFoto(Request $request)
	{
		if (!$request->hasFile('foto')) {
			return null;
		}

		$file = $request->file('foto');
		$destination = $this->uploadPath('menu-harian');
		$filename = time().'_'.uniqid().'_'.str_replace(' ', '-', $file->getClientOriginalName());

		if (!File::isDirectory($destination)) {
			File::makeDirectory($destination, 0777, true, true);
		}

		$file->move($destination, $filename);

		return 'menu-harian/'.$filename;
	}

	private function deleteFoto(MenuHarian $menuharian)
	{
		if (!$menuharian->foto) {
			return;
		}

		$path = $this->uploadPath($menuharian->foto);

		if (File::exists($path)) {
			File::delete($path);
			return;
		}

		if (Storage::disk('public')->exists($menuharian->foto)) {
			Storage::disk('public')->delete($menuharian->foto);
		}
	}

	private function fotoUrl($path)
	{
		return asset('po-content/uploads/'.$path);
	}

	private function uploadPath($path = '')
	{
		return str_replace(['\po-includes','/po-includes'], '', base_path('po-content/uploads/'.$path));
	}

	private function nutritionSummary(MenuHarian $menuharian, $prefix)
	{
		$energi = $menuharian->{$prefix.'_energi'};
		$protein = $menuharian->{$prefix.'_protein'};

		if ($energi === null && $protein === null) {
			return '-';
		}

		return ($energi !== null ? rtrim(rtrim(number_format($energi, 2, '.', ''), '0'), '.') . ' kkal' : '-')
			. ' | Protein '
			. ($protein !== null ? rtrim(rtrim(number_format($protein, 2, '.', ''), '0'), '.') . 'g' : '-');
	}

	private function canAccess($action)
	{
		return Auth::user()->can($action.'-menu-harians');
	}

	private function canList()
	{
		return $this->canAccess('read')
			&& ($this->isAdminUser() || (!$this->isSchoolUser() && $this->activeSppgId()));
	}

	private function canCreateMenu()
	{
		return $this->canAccess('create')
			&& ($this->isAdminUser() || (!$this->isSchoolUser() && $this->activeSppgId()));
	}

	private function canViewMenu(MenuHarian $menuharian)
	{
		if ($this->isAdminUser()) {
			return true;
		}

		$sppgId = $this->activeSppgId();
		if ($sppgId) {
			return (int) $menuharian->sppg_id === (int) $sppgId;
		}

		$sekolahId = $this->activeSekolahId();
		if ($sekolahId) {
			return $menuharian->distribusis()
				->where('sekolah_id', $sekolahId)
				->exists();
		}

		return false;
	}

	private function canModifyMenu(MenuHarian $menuharian)
	{
		if ($this->isAdminUser()) {
			return true;
		}

		if ($this->isSchoolUser()) {
			return false;
		}

		$sppgId = $this->activeSppgId();

		return $sppgId && (int) $menuharian->sppg_id === (int) $sppgId;
	}

	private function visibleMenuHarians($query)
	{
		if ($this->isAdminUser()) {
			return $query;
		}

		$sppgId = $this->activeSppgId();
		if ($sppgId) {
			return $query->where('sppg_id', $sppgId);
		}

		$sekolahId = $this->activeSekolahId();
		if ($sekolahId) {
			return $query->whereHas('distribusis', function ($q) use ($sekolahId) {
				$q->where('sekolah_id', $sekolahId);
			});
		}

		return $query->whereRaw('1 = 0');
	}

	private function resolveInputSppgId(Request $request, MenuHarian $menuharian = null)
	{
		if ($this->isAdminUser()) {
			return $request->input('sppg_id');
		}

		if ($this->activeSppgId()) {
			return $this->activeSppgId();
		}

		return optional($menuharian)->sppg_id;
	}

	private function availableDistribusiSekolahs($sppgId = null)
	{
		if (!$this->isAdminUser()) {
			$sppgId = $this->activeSppgId();
		}

		if ($sppgId) {
			$sppg = Sppg::find($sppgId);
			return $sppg ? $sppg->sekolahs()->orderBy('nama')->get() : collect();
		}

		return Sekolah::orderBy('nama')->get();
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
