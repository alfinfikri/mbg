<?php

namespace App\Http\Controllers\Backend;

use App\Aduan;
use App\Http\Controllers\Controller;
use App\Sppg;
use App\User;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Vinkla\Hashids\Facades\Hashids;
use Yajra\Datatables\Datatables;

class AduanController extends Controller
{
	public function index(Request $request)
	{
		if ($this->canRead()) {
			return view('backend.aduan.datatable');
		}

		return redirect('forbidden');
	}

	public function getIndex()
	{
		if ($this->canRead()) {
			return view('backend.aduan.datatable');
		}

		return redirect('forbidden');
	}

	public function anyData()
	{
		if (!$this->canRead()) {
			return response()->json(['error' => 'Forbidden'], 403);
		}

		$aduans = $this->scopedAduanQuery()->with(['disposisiSppg', 'disposisiUser', 'disposisiSatgas'])->orderBy('id', 'desc');

		return Datatables::of($aduans)
			->addColumn('tanggal', function ($s) {
				return $s->tgl_aduan ? Carbon::parse($s->tgl_aduan)->format('d-m-Y') : '-';
			})
			->addColumn('nama', fn($s) => $s->nama)
			->addColumn('no_hp', fn($s) => $this->maskPhone($s->no_hp))
			->addColumn('isi_aduan', fn($s) => Str::limit(strip_tags($s->isi_aduan), 90))
			->addColumn('status', fn($s) => $this->statusBadge($s->status_pengaduan ?? $s->status))
			->addColumn('action', function ($post) {
				$id = Hashids::encode($post->id);
				$btn = '<div style="text-align:center;"><div class="btn-group">';

				if ($this->canRead() && $this->canAccessAduan($post)) {
					$btn .= '<a href="'.url('dashboard/aduans/'.$id).'" class="btn btn-secondary btn-xs btn-icon" title="'.__('general.view').'" data-toggle="tooltip" data-placement="left"><i class="fa fa-eye"></i></a>';
				}

				if ($this->canDo('disposisi-pengaduans')) {
					$btn .= '<a href="'.url('dashboard/aduans/'.$id.'/disposisi').'" class="btn btn-primary btn-xs btn-icon" title="Disposisi" data-toggle="tooltip" data-placement="left"><i class="fa fa-share"></i></a>';
				}

				if ($this->canProcessAction()) {
					$btn .= '<a href="'.url('dashboard/aduans/'.$id.'/proses').'" class="btn btn-warning btn-xs btn-icon" title="Proses" data-toggle="tooltip" data-placement="left"><i class="fa fa-comments"></i></a>';
				}

				if ($this->canDo('followup-pengaduans') && $this->canFollowupAduan($post)) {
					$btn .= '<a href="'.url('dashboard/aduans/'.$id.'/tindak-lanjut').'" class="btn btn-info btn-xs btn-icon" title="Tindak Lanjut" data-toggle="tooltip" data-placement="left"><i class="fa fa-wrench"></i></a>';
				}

				$btn .= '</div></div>';
				return $btn;
			})
			->addColumn('control', fn() => '')
			->rawColumns(['status', 'action'])
			->make(true);
	}

	public function show($id)
	{
		if (!$this->canRead()) {
			return redirect('forbidden');
		}

		$aduan = $this->findAduan($id);
		$this->authorizeAduan($aduan);

		return view('backend.aduan.show', $this->showData($aduan));
	}

	public function create()
	{
		return redirect('forbidden');
	}

	public function store(Request $request)
	{
		return redirect('forbidden');
	}

	public function edit($id)
	{
		return $this->show($id);
	}

	public function update(Request $request, $id)
	{
		return redirect('forbidden');
	}

	public function disposisi($id)
	{
		if (!$this->canDo('disposisi-pengaduans')) {
			return redirect('forbidden');
		}

		$aduan = $this->findAduan($id);
		$mode = 'disposisi';

		return view('backend.aduan.show', $this->showData($aduan, $mode));
	}

	public function simpanDisposisi(Request $request, $id)
	{
		if (!$this->canDo('disposisi-pengaduans')) {
			return redirect('forbidden');
		}

		if (!$request->has('disposisi_satgas_ids') && $request->filled('disposisi_satgas_id')) {
			$request->merge(['disposisi_satgas_ids' => [$request->disposisi_satgas_id]]);
		}

		$request->validate([
			'disposisi_satgas_ids' => 'required|array|min:1|max:2',
			'disposisi_satgas_ids.*' => [
				'required',
				'distinct',
				Rule::in($this->satgasUsers()->pluck('id')->all()),
			],
			'catatan_disposisi' => 'nullable|string',
		]);

		$aduan = $this->findAduan($id);
		$satgasIds = collect($request->input('disposisi_satgas_ids', []))->filter()->map(fn($id) => (int) $id)->values()->all();

		DB::transaction(function () use ($aduan, $request, $satgasIds) {
			$aduan->update([
				'user_id' => null,
				'disposisi_user_id' => null,
				'disposisi_sppg_id' => null,
				'disposisi_satgas_id' => $satgasIds[0] ?? null,
				'disposisi_satgas_ids' => $satgasIds,
				'catatan_disposisi' => $request->catatan_disposisi,
				'disposisi_at' => now(),
				'tgl_disposisi' => now(),
				'status' => 1,
				'status_pengaduan' => 1,
				'updated_by' => Auth::id(),
			]);

			$satgasNames = User::whereIn('id', $satgasIds)->orderBy('name')->pluck('name')->implode(', ');
			$message = 'Aduan telah didisposisikan kepada '.$satgasNames.'.';
			if ($request->catatan_disposisi) {
				$message .= "\n\n".$request->catatan_disposisi;
			}

			$this->logActivity($aduan, 'Aduan Di Disposisikan', $message);
		});

		return redirect('dashboard/aduans/table')->with('flash_message', 'Aduan berhasil didisposisikan');
	}

	public function proses($id)
	{
		if (!$this->canProcessAction()) {
			return redirect('forbidden');
		}

		$aduan = $this->findAduan($id);
		$this->authorizeAduan($aduan);
		$mode = 'proses';

		return view('backend.aduan.show', $this->showData($aduan, $mode));
	}

	public function simpanProses(Request $request, $id)
	{
		if (!$this->canProcessAction()) {
			return redirect('forbidden');
		}

		$request->validate([
			'tanggapan' => 'required|string',
			'disposisi_sppg_id' => 'nullable|exists:sppgs,id',
			'disposisi_user_id' => 'nullable|exists:users,id',
			'catatan_teknis' => 'nullable|string',
		]);

		$aduan = $this->findAduan($id);
		$this->authorizeAduan($aduan);

		DB::transaction(function () use ($aduan, $request) {
			$responSatgas = $aduan->respon_satgas ?: [];
			$responSatgas[(string) Auth::id()] = [
				'user_id' => Auth::id(),
				'name' => Auth::user()->name,
				'tanggapan' => $request->tanggapan,
				'responded_at' => now()->toDateTimeString(),
			];
			$responProses = $this->formatResponSatgas($responSatgas);

			$aduan->update([
				'tanggapan' => $request->tanggapan,
				'respon_proses' => $responProses,
				'respon_satgas' => $responSatgas,
				'disposisi_sppg_id' => $request->disposisi_sppg_id,
				'disposisi_user_id' => $request->disposisi_user_id,
				'user_id' => $request->disposisi_user_id,
				'catatan_disposisi' => $this->mergeCatatanDisposisi($aduan->catatan_disposisi, $request->catatan_teknis),
				'tgl_proses' => now(),
				'status' => 2,
				'status_pengaduan' => 2,
				'updated_by' => Auth::id(),
			]);

			$logMessage = 'Satgas '.Auth::user()->name.' telah menyimpan tanggapan.';
			if ($request->filled('disposisi_sppg_id') || $request->filled('disposisi_user_id')) {
				$logMessage .= "\n\nDiteruskan untuk tindak lanjut teknis SPPG.";
			}
			$this->logActivity($aduan, 'Aduan Direspon oleh Satgas: '.Auth::user()->name, $logMessage);
		});

		return back()->with('flash_message', 'Tanggapan berhasil disimpan');
	}

	public function tindakLanjut($id)
	{
		if (!$this->canDo('followup-pengaduans')) {
			return redirect('forbidden');
		}

		$aduan = $this->findAduan($id);
		if (!$this->canFollowupAduan($aduan)) {
			return redirect('forbidden');
		}
		$mode = 'tindak-lanjut';

		return view('backend.aduan.show', $this->showData($aduan, $mode));
	}

	public function simpanTindakLanjut(Request $request, $id)
	{
		if (!$this->canDo('followup-pengaduans')) {
			return redirect('forbidden');
		}

		$request->validate([
			'tanggapan_sppg' => 'required|string',
			'foto_tindak_lanjut' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
		]);

		$aduan = $this->findAduan($id);
		if (!$this->canFollowupAduan($aduan)) {
			return redirect('forbidden');
		}

		DB::transaction(function () use ($aduan, $request) {
			$sppgName = $this->sppgNameForAduan($aduan);
			$data = [
				'tanggapan_sppg' => $request->tanggapan_sppg,
				'ditindaklanjuti_at' => now(),
				'status' => 2,
				'status_pengaduan' => 2,
				'updated_by' => Auth::id(),
			];

			if ($request->hasFile('foto_tindak_lanjut')) {
				$this->deleteUploadedFile($aduan->foto_tindak_lanjut);
				$data['foto_tindak_lanjut'] = $this->storeFotoTindakLanjut($request);
			}

			$aduan->update($data);
			$this->logActivity($aduan, 'Tindak Lanjut SPPG: '.$sppgName, $sppgName.' telah menyimpan tindak lanjut aduan.');
		});

		return back()->with('flash_message', 'Tindak lanjut berhasil disimpan');
	}

	public function close($id)
	{
		if (!$this->canCloseAction()) {
			return redirect('forbidden');
		}

		$aduan = $this->findAduan($id);
		$this->authorizeAduan($aduan);

		$aduan->update([
			'status' => 3,
			'status_pengaduan' => 3,
			'tgl_selesai' => now(),
			'closed_by' => Auth::id(),
			'closed_at' => now(),
			'updated_by' => Auth::id(),
		]);

		$this->logActivity($aduan, 'Aduan Diselesaikan oleh Superadmin', 'Aduan telah ditutup sebagai selesai oleh superadmin.');

		return back()->with('flash_message', 'Aduan berhasil diselesaikan');
	}

	public function reject(Request $request, $id)
	{
		if (!$this->canRejectAction()) {
			return redirect('forbidden');
		}

		$request->validate([
			'tanggapan' => 'nullable|string',
		]);

		$aduan = $this->findAduan($id);
		$this->authorizeAduan($aduan);

		$aduan->update([
			'status' => 4,
			'status_pengaduan' => 4,
			'tanggapan' => $request->tanggapan ?: $aduan->tanggapan,
			'updated_by' => Auth::id(),
		]);

		$this->logActivity($aduan, 'Aduan Ditolak', $request->tanggapan ?: 'Aduan ditolak atau tidak dapat diproses.');

		return back()->with('flash_message', 'Aduan berhasil ditolak');
	}

	public function destroy($id)
	{
		if (!$this->canDo('delete-pengaduans') && !$this->canDo('delete-aduans')) {
			return redirect('forbidden');
		}

		$aduan = $this->findAduan($id);
		$aduan->delete();

		return redirect('dashboard/aduans/table')->with('flash_message', __('general.deleted_successfully'));
	}

	public function deleteAll(Request $request)
	{
		if (!$this->canDo('delete-pengaduans') && !$this->canDo('delete-aduans')) {
			return redirect('forbidden');
		}

		$ids = $request->ids ?: $request->id;
		if (is_array($ids)) {
			Aduan::whereIn('id', $ids)->delete();
		}

		return back()->with('flash_message', __('general.deleted_successfully'));
	}

	public function prosesAduan(Request $request, $id)
	{
		return $this->simpanDisposisi($request, $id);
	}

	public function responAduan(Request $request, $id)
	{
		$request->merge(['tanggapan' => $request->input('respon')]);
		return $this->simpanProses($request, $id);
	}

	public function responAkhir(Request $request, $id)
	{
		if (!$this->canCloseAction()) {
			return redirect('forbidden');
		}

		if ($request->filled('respon_selesai')) {
			$aduan = $this->findAduan($id);
			$aduan->update([
				'respon_selesai' => $request->respon_selesai,
				'tanggapan' => $request->respon_selesai,
			]);
		}

		return $this->close($id);
	}

	private function scopedAduanQuery()
	{
		$user = Auth::user();
		$query = Aduan::query();

		if ($user->sekolah_id && !$this->canDo('read-own-pengaduans')) {
			return $query->whereRaw('1 = 0');
		}

		if ($this->isSuperAdminRole()) {
			return $query;
		}

		if ($this->canDo('followup-pengaduans') && $user->sppg_id) {
			return $query->where(function ($q) use ($user) {
				$q->where('disposisi_sppg_id', $user->sppg_id)
					->orWhere('disposisi_user_id', $user->id)
					->orWhere('user_id', $user->id);
			});
		}

		if ($this->canProcessAction()) {
			return $query->where(function ($q) use ($user) {
				$this->whereAssignedToSatgas($q, $user->id);
			});
		}

		return $query->whereRaw('1 = 0');
	}

	private function whereAssignedToSatgas($query, $userId)
	{
		$id = (int) $userId;

		return $query->where('disposisi_satgas_id', $id)
			->orWhere(function ($satgasQuery) use ($id) {
				$satgasQuery->where('disposisi_satgas_ids', '['.$id.']')
					->orWhere('disposisi_satgas_ids', 'like', '['.$id.',%')
					->orWhere('disposisi_satgas_ids', 'like', '['.$id.', %')
					->orWhere('disposisi_satgas_ids', 'like', '%,'.$id.',%')
					->orWhere('disposisi_satgas_ids', 'like', '%, '.$id.',%')
					->orWhere('disposisi_satgas_ids', 'like', '%,'.$id.', %')
					->orWhere('disposisi_satgas_ids', 'like', '%, '.$id.', %')
					->orWhere('disposisi_satgas_ids', 'like', '%,'.$id.']')
					->orWhere('disposisi_satgas_ids', 'like', '%, '.$id.']');
			});
	}

	private function showData(Aduan $aduan, $mode = null)
	{
		$satgasUsers = $this->satgasUsers();
		$sppgUsers = $this->usersWithPermission('followup-pengaduans');
		$sppgs = Sppg::orderBy('nama')->pluck('nama', 'id');
		$isAdmin = $this->canDo('disposisi-pengaduans');
		$canDisposisi = $this->canDo('disposisi-pengaduans');
		$canProcess = $this->canProcessAction();
		$canFollowup = $this->canDo('followup-pengaduans') && !$this->isSuperAdminRole();
		$selectedSatgasIds = old('disposisi_satgas_ids', $this->aduanSatgasIds($aduan));
		$satgasResponses = $aduan->respon_satgas ?: [];
		$canClose = $this->canCloseAction();
		$canReject = $this->canRejectAction();

		return compact('aduan', 'satgasUsers', 'sppgUsers', 'sppgs', 'isAdmin', 'mode', 'canDisposisi', 'canProcess', 'canFollowup', 'canClose', 'canReject', 'selectedSatgasIds', 'satgasResponses');
	}

	private function usersWithPermission($permission)
	{
		return User::join('model_has_roles', function ($join) {
				$join->on('users.id', '=', 'model_has_roles.model_id')
					->where('model_has_roles.model_type', 'App\\User');
			})
			->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
			->join('role_has_permissions', 'role_has_permissions.role_id', '=', 'roles.id')
			->join('permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
			->where('permissions.name', $permission)
			->where('permissions.guard_name', 'web')
			->select('users.*')
			->distinct()
			->orderBy('users.name')
			->get();
	}

	private function satgasUsers()
	{
		return User::join('model_has_roles', function ($join) {
				$join->on('users.id', '=', 'model_has_roles.model_id')
					->where('model_has_roles.model_type', 'App\\User');
			})
			->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
			->where('roles.name', 'admin')
			->where('roles.guard_name', 'web')
			->select('users.*')
			->distinct()
			->orderBy('users.name')
			->get();
	}

	private function findAduan($id)
	{
		$ids = Hashids::decode($id);
		$aduanId = count($ids) ? $ids[0] : $id;

		return Aduan::with(['wilayah.parent', 'sppg', 'sekolah', 'user', 'disposisiUser', 'disposisiSppg', 'disposisiSatgas', 'closedBy'])->findOrFail($aduanId);
	}

	private function authorizeAduan(Aduan $aduan)
	{
		if (!$this->canAccessAduan($aduan)) {
			abort(403);
		}
	}

	private function canAccessAduan(Aduan $aduan)
	{
		return $this->scopedAduanQuery()->where('id', $aduan->id)->exists();
	}

	private function canFollowupAduan(Aduan $aduan)
	{
		$user = Auth::user();

		if (!$this->canDo('followup-pengaduans')) {
			return false;
		}

		return ($user->sppg_id && (int) $aduan->disposisi_sppg_id === (int) $user->sppg_id)
			|| (int) $aduan->disposisi_user_id === (int) $user->id
			|| (int) $aduan->user_id === (int) $user->id;
	}

	private function canProcessAction()
	{
		return $this->canDo('process-pengaduans') && !$this->isSuperAdminRole();
	}

	private function canCloseAction()
	{
		return $this->canDo('close-pengaduans') && $this->isSuperAdminRole();
	}

	private function canRejectAction()
	{
		return $this->canDo('reject-pengaduans') && $this->isSuperAdminRole();
	}

	private function isSuperAdminRole()
	{
		$user = Auth::user();

		return $user && ($user->hasRole('superadmin') || $user->hasRole('superadmin 2'));
	}

	private function canRead()
	{
		$user = Auth::user();

		if ($user->sekolah_id && !$this->canDo('read-own-pengaduans')) {
			return false;
		}

		return $this->canDo('read-pengaduans') || $this->canDo('read-aduans');
	}

	private function canDo($permission)
	{
		$user = Auth::user();

		if (!$user) {
			return false;
		}

		if ($user->can($permission)) {
			return true;
		}

		return $user->getAllPermissions()->contains('name', $permission);
	}

	private function mergeCatatanDisposisi($existing, $catatanTeknis)
	{
		if (!$catatanTeknis) {
			return $existing;
		}

		$prefix = 'Catatan teknis dari Satgas untuk SPPG : '.$catatanTeknis;

		return trim($existing ? $existing."\n\n".$prefix : $prefix);
	}

	private function aduanSatgasIds(Aduan $aduan)
	{
		$ids = $aduan->disposisi_satgas_ids ?: [];

		if (empty($ids) && $aduan->disposisi_satgas_id) {
			$ids = [$aduan->disposisi_satgas_id];
		}

		return collect($ids)->filter()->map(fn($id) => (int) $id)->values()->all();
	}

	private function formatResponSatgas(array $responSatgas)
	{
		return collect($responSatgas)->map(function ($response) {
			$name = $response['name'] ?? 'Satgas';
			$time = !empty($response['responded_at'])
				? Carbon::parse($response['responded_at'])->format('d-m-Y H:i')
				: '-';
			$tanggapan = $response['tanggapan'] ?? '-';

			return $name.' ('.$time.'):'."\n".$tanggapan;
		})->implode("\n\n");
	}

	private function statusBadge($status)
	{
		$map = [
			0 => '<span class="badge badge-secondary">Baru</span>',
			1 => '<span class="badge badge-info">Didisposisikan</span>',
			2 => '<span class="badge badge-warning">Diproses</span>',
			3 => '<span class="badge badge-success">Selesai</span>',
			4 => '<span class="badge badge-danger">Ditolak</span>',
		];

		return $map[(int) $status] ?? '-';
	}

	private function maskPhone($phone)
	{
		if (!$phone) {
			return '-';
		}

		return substr($phone, 0, 4).'****'.substr($phone, -3);
	}

	private function storeFotoTindakLanjut(Request $request)
	{
		$file = $request->file('foto_tindak_lanjut');
		$destination = $this->uploadPath('aduan/tindak-lanjut');
		$filename = time().'_'.uniqid().'_'.str_replace(' ', '-', $file->getClientOriginalName());

		if (!File::isDirectory($destination)) {
			File::makeDirectory($destination, 0777, true, true);
		}

		$file->move($destination, $filename);

		return 'aduan/tindak-lanjut/'.$filename;
	}

	private function deleteUploadedFile($path)
	{
		if (!$path) {
			return;
		}

		$file = $this->uploadPath($path);
		if (File::exists($file)) {
			File::delete($file);
		}
	}

	private function uploadPath($path = '')
	{
		return str_replace(['\po-includes', '/po-includes'], '', base_path('po-content/uploads/'.$path));
	}

	private function sppgNameForAduan(Aduan $aduan)
	{
		$user = Auth::user();

		return optional(optional($user)->sppg)->nama
			?? optional($aduan->disposisiSppg)->nama
			?? optional($aduan->sppg)->nama
			?? 'SPPG';
	}

	private function logActivity(Aduan $aduan, $description, $message)
	{
		if (!function_exists('activity')) {
			return;
		}

		activity()->performedOn($aduan)
			->withProperties([
				'keterangan' => $message,
				'kode_tiket' => $aduan->kode_tiket,
			])
			->log($description);
	}
}
