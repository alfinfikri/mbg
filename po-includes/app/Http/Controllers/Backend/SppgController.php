<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Sekolah;
use App\Sppg;
use App\Wilayah;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Yajra\Datatables\Datatables;
use Vinkla\Hashids\Facades\Hashids;

class SppgController extends Controller
{
    public function index(Request $request)
    {
		if(Auth::user()->can('read-sppgs')) {
			return view('backend.sppg.datatable');
		} else {
			return redirect('forbidden');
		}
    }

    public function getIndex()
	{
		if(Auth::user()->can('read-sppgs')) {
			return view('backend.sppg.datatable');
		} else {
			return redirect('forbidden');
		}
	}

	public function anyData()
	{
		$sppgs = Sppg::with(['wilayah.parent', 'sekolahs:id,nama,jumlah_total'])
			->withCount('sekolahs')
			->orderBy('id', 'desc');

		return Datatables::of($sppgs)
			->addColumn('check', function ($sppg) {
				return '<div style="text-align:center;">
					<input type="checkbox" />
					<input type="hidden" class="deldata" name="id[]" value="'.Hashids::encode($sppg->id).'" disabled />
				</div>';
			})
			->addColumn('nama', fn($s) => $s->nama)
			->addColumn('wilayah', function ($s) {
				if (!$s->wilayah) return '-';

				$kelurahan = $s->wilayah->nama_wilayah;
				$kecamatan = optional($s->wilayah->parent)->nama_wilayah;

				return $kecamatan
					? $kelurahan . ' - ' . $kecamatan
					: $kelurahan;
			})
			->addColumn('jumlah_sekolah', fn($s) => $s->sekolahs_count)
			->addColumn('total_penerima', fn($s) => $s->sekolahs->sum('jumlah_total'))
			->addColumn('kapasitas_produksi', fn($s) => $s->kapasitas_produksi)
			->addColumn('status_layanan', fn($s) => $this->statusBadge($s->status_layanan))
            ->addColumn('action', function ($sppg) {
				$btn = '<div style="text-align:center;"><div class="btn-group">';
				$btn .= '<a href="'.url('dashboard/sppgs/'.Hashids::encode($sppg->id)).'" class="btn btn-info btn-xs btn-icon" title="'.__('general.show').'" data-toggle="tooltip" data-placement="left"><i class="fa fa-eye"></i></a>';
				$btn .= '<a href="'.url('dashboard/sppgs/'.Hashids::encode($sppg->id).'/edit').'" class="btn btn-primary btn-xs btn-icon" title="'.__('general.edit').'" data-toggle="tooltip" data-placement="left"><i class="fa fa-edit"></i></a>';
				$btn .= '<a href="'.url('dashboard/sppgs/'.Hashids::encode($sppg->id).'').'" class="btn btn-danger btn-xs btn-icon" data-delete="" title="'.__('general.delete').'" data-toggle="tooltip" data-placement="left"><i class="fa fa-trash"></i></a>';
				$btn .= '</div></div>';
				return $btn;
            })
			->addColumn('control', function ($sppg) {
				return '<div style="text-align:center;"><a href="javascript:void(0);" class="btn btn-secondary btn-xs btn-icon" data-placement="left"><i class="fa fa-plus"></i></a></div>';
			})
			->escapeColumns([])
			->rawColumns(['status_layanan', 'action'])
			->make(true);
	}

    public function create()
    {
		if(Auth::user()->can('create-sppgs')) {
   			$kecamatans = Wilayah::where('child_id', 0)->pluck('nama_wilayah', 'id');
			$sekolahs = $this->availableSekolahs()->pluck('nama', 'id');

			return view('backend.sppg.create', [
				'sppg' => null,
				'kecamatans' => $kecamatans,
				'sekolahs' => $sekolahs,
				'selectedSekolahIds' => []
			]);
		} else {
			return redirect('forbidden');
		}
    }

    public function store(Request $request)
	{
		if (!Auth::user()->can('create-sppgs')) {
			return redirect('forbidden');
		}

		$this->validateSppg($request);
		$this->ensureSekolahAvailable($request->input('sekolah_ids', []));

		try {
			DB::transaction(function () use ($request) {
				$sppg = Sppg::create($this->sppgData($request) + [
					'created_by' => Auth::id(),
					'updated_by' => Auth::id(),
				]);

				$sppg->sekolahs()->sync($request->input('sekolah_ids', []));
			});

			return redirect('dashboard/sppgs')
				->with('flash_message', 'Data sppg berhasil disimpan');

		} catch (\Exception $e) {
			return redirect()->back()
				->with('error_message', $e->getMessage())
				->withInput();
		}
	}

    public function show($id)
    {
		if(Auth::user()->can('read-sppgs')) {
			$ids = Hashids::decode($id);
			$sppg = Sppg::with(['wilayah.parent', 'sekolahs.wilayah'])->findOrFail($ids[0]);
			$totalPenerima = $sppg->sekolahs->sum('jumlah_total');

			return view('backend.sppg.show', compact('sppg', 'totalPenerima'));
		} else {
			return redirect('forbidden');
		}
    }

    public function edit($id)
    {
		if(Auth::user()->can('update-sppgs')) {
			$ids = Hashids::decode($id);
			$sppg = Sppg::with('sekolahs')->findOrFail($ids[0]);
			$kecamatans = Wilayah::where('child_id', 0)->pluck('nama_wilayah', 'id');
			$selectedSekolahIds = $sppg->sekolahs->pluck('id')->toArray();
			$sekolahs = $this->availableSekolahs($sppg)->pluck('nama', 'id');

			return view('backend.sppg.edit', compact('sppg', 'kecamatans', 'sekolahs', 'selectedSekolahIds'));
		} else {
			return redirect('forbidden');
		}
    }

    public function update(Request $request, $id)
	{
		if (!Auth::user()->can('update-sppgs')) {
			return redirect('forbidden')
				->with('error_message', 'Tidak punya akses');
		}

		$decoded = Hashids::decode($id);
		$id = $decoded[0] ?? null;

		if (!$id) {
			return redirect()->back()
				->with('error_message', 'ID tidak valid');
		}

		$sppg = Sppg::findOrFail($id);
		$this->validateSppg($request);
		$this->ensureSekolahAvailable($request->input('sekolah_ids', []), $sppg->id);

		try {
			DB::transaction(function () use ($request, $sppg) {
				$sppg->update($this->sppgData($request) + [
					'updated_by' => Auth::id(),
				]);

				$sppg->sekolahs()->sync($request->input('sekolah_ids', []));
			});

			return redirect('dashboard/sppgs')
				->with('flash_message', 'Data sppg berhasil diupdate');

		} catch (\Exception $e) {
			return redirect()->back()
				->with('error_message', $e->getMessage())
				->withInput();
		}
	}

    public function destroy($id)
    {
		if(Auth::user()->can('delete-sppgs')) {
			$ids = Hashids::decode($id);
			$sppg = Sppg::findOrFail($ids[0]);
			$sppg->sekolahs()->detach();
			$sppg->delete();

			return redirect('dashboard/sppgs')->with('flash_message', 'Data berhasil dihapus');
		} else {
			return redirect('forbidden');
		}
    }

    public function deleteAll(Request $request)
    {
		if(Auth::user()->can('delete-sppgs')) {
			if ($request->has('id')) {
				$ids = $request->id;
				foreach($ids as $id){
					$idd = Hashids::decode($id);
					$sppg = Sppg::find($idd[0]);

					if ($sppg) {
						$sppg->sekolahs()->detach();
						$sppg->delete();
					}
				}
				return redirect('dashboard/sppgs')->with('flash_message', 'Data berhasil dihapus');
			} else {
				return redirect('dashboard/sppgs')->with('flash_message', 'Data mu aman, belum dihapus');
			}
		} else {
			return redirect('forbidden');
		}
    }

	public function getSppg(Request $request)
	{
		$sppgs = Sppg::select('id', 'nama')
			->where('status_layanan', 1)
			->when($request->term, function ($q) use ($request) {
				$q->where('nama', 'like', '%' . $request->term . '%');
			})
			->limit(10)
			->get();

		return response()->json([
			'data' => $sppgs
		]);
	}

	private function validateSppg(Request $request)
	{
		$request->validate([
			'nama' => 'required|string|max:255',
			'wilayah_id' => 'nullable',
			'alamat' => 'nullable|string',
			'latitude' => 'nullable|numeric',
			'longitude' => 'nullable|numeric',
			'nama_penanggung_jawab' => 'nullable|string|max:255',
			'no_hp' => 'nullable|string|max:255',
			'email' => 'nullable|email|max:255',
			'slhs_nomor' => 'nullable|string|max:255',
			'slhs_tanggal' => 'nullable|date',
			'slhs_tanggal_terbit' => 'nullable|date',
			'slhs_berlaku_hingga' => 'nullable|date',
			'slhs_file' => 'nullable|string|max:255',
			'halal_nomor' => 'nullable|string|max:255',
			'halal_tanggal_terbit' => 'nullable|date',
			'halal_file' => 'nullable|string|max:255',
			'foto_dapur' => 'nullable|string|max:255',
			'nama_ahli_gizi' => 'nullable|string|max:255',
			'keterangan_data_profil' => 'nullable|string',
			'fasilitas_dapur' => 'nullable|string',
			'kapasitas_produksi' => 'nullable|numeric|min:0',
			'jumlah_petugas' => 'nullable|numeric|min:0',
			'status_layanan' => 'required|in:1,2,3',
			'sekolah_ids' => 'nullable|array',
			'sekolah_ids.*' => 'exists:sekolahs,id',
		]);
	}

	private function sppgData(Request $request)
	{
		$data = [
			'nama' => $request->nama,
			'wilayah_id' => $request->wilayah_id,
			'alamat' => $request->alamat,
			'latitude' => $request->latitude,
			'longitude' => $request->longitude,
			'nama_penanggung_jawab' => $request->nama_penanggung_jawab,
			'no_hp' => $request->no_hp,
			'email' => $request->email,
			'slhs_nomor' => $request->slhs_nomor,
			'slhs_tanggal' => $request->slhs_tanggal_terbit ?: $request->slhs_tanggal,
			'slhs_file' => $request->slhs_file,
			'foto_dapur' => $request->foto_dapur,
			'nama_ahli_gizi' => $request->nama_ahli_gizi,
			'keterangan_data_profil' => $request->keterangan_data_profil,
			'fasilitas_dapur' => $request->fasilitas_dapur,
			'kapasitas_produksi' => (int) $request->kapasitas_produksi,
			'jumlah_petugas' => (int) $request->jumlah_petugas,
			'status_layanan' => $request->status_layanan,
		];

		if (Schema::hasColumn('sppgs', 'slhs_tanggal_terbit')) {
			$data['slhs_tanggal_terbit'] = $request->slhs_tanggal_terbit ?: $request->slhs_tanggal;
		}

		if (Schema::hasColumn('sppgs', 'slhs_berlaku_hingga')) {
			$data['slhs_berlaku_hingga'] = $request->slhs_berlaku_hingga;
		}

		if (Schema::hasColumn('sppgs', 'halal_nomor')) {
			$data['halal_nomor'] = $request->halal_nomor;
		}

		if (Schema::hasColumn('sppgs', 'halal_tanggal_terbit')) {
			$data['halal_tanggal_terbit'] = $request->halal_tanggal_terbit;
		}

		if (Schema::hasColumn('sppgs', 'halal_file')) {
			$data['halal_file'] = $request->halal_file;
		}

		return $data;
	}

	private function statusBadge($status)
	{
		$labels = [
			1 => ['success', 'Aktif'],
			2 => ['warning', 'Tidak Aktif'],
			3 => ['secondary', 'Belum Operasi'],
		];

		$data = $labels[$status] ?? ['secondary', 'Tidak Ada Data'];

		return '<span class="badge badge-' . $data[0] . '">' . $data[1] . '</span>';
	}

	private function availableSekolahs(Sppg $sppg = null)
	{
		$selectedSekolahIds = $sppg
			? $sppg->sekolahs()->pluck('sekolahs.id')->toArray()
			: [];

		return Sekolah::where(function ($query) use ($selectedSekolahIds) {
				$query->whereDoesntHave('sppgs');

				if (!empty($selectedSekolahIds)) {
					$query->orWhereIn('id', $selectedSekolahIds);
				}
			})
			->where(function ($query) use ($selectedSekolahIds) {
				$query->where('status_layanan', 1);

				if (!empty($selectedSekolahIds)) {
					$query->orWhereIn('id', $selectedSekolahIds);
				}
			})
			->orderBy('nama')
			->get();
	}

	private function ensureSekolahAvailable(array $sekolahIds, $currentSppgId = null)
	{
		if (empty($sekolahIds)) {
			return;
		}

		$conflicted = DB::table('sppg_sekolahs')
			->whereIn('sekolah_id', $sekolahIds)
			->when($currentSppgId, function ($query) use ($currentSppgId) {
				$query->where('sppg_id', '!=', $currentSppgId);
			})
			->pluck('sekolah_id')
			->all();

		if (!empty($conflicted)) {
			$names = Sekolah::whereIn('id', $conflicted)->pluck('nama')->implode(', ');

			throw ValidationException::withMessages([
				'sekolah_ids' => 'Sekolah berikut sudah terhubung dengan SPPG lain: '.$names,
			]);
		}
	}
}
