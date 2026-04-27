<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Sekolah;
use App\Wilayah;

use Yajra\Datatables\Datatables;
use Vinkla\Hashids\Facades\Hashids;

class SekolahController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return void
     */
    public function index(Request $request)
    {
		if(Auth::user()->can('read-sekolahs')) {
			return view('backend.sekolah.datatable');
		} else {
			return redirect('forbidden');
		}
    }
	
	/**
	 * Displays datatables front end view
	 *
	 * @return \Illuminate\View\View
	 */
    public function getIndex()
	{
		if(Auth::user()->can('read-sekolahs')) {
			return view('backend.sekolah.datatable');
		} else {
			return redirect('forbidden');
		}
	}
	
	/**
	 * Process datatables ajax request.
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function anyData()
	{
		$sekolahs = Sekolah::with('wilayah.parent')->orderBy('id', 'desc');

		return Datatables::of($sekolahs)
			->addColumn('check', function ($sekolah) {
				return '<div style="text-align:center;">
					<input type="checkbox" />
					<input type="hidden" class="deldata" name="id[]" value="'.Hashids::encode($sekolah->id).'" disabled />
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
			->addColumn('jumlah_total', fn($s) => $s->jumlah_total)
			->addColumn('status_layanan', fn($s) => $s->status_layanan
				? '<span class="badge badge-' . ($s->status_layanan == 1 ? 'success' : ($s->status_layanan == 2 ? 'warning' : 'danger')) . '">' . ($s->status_layanan == 1 ? 'Aktif' : ($s->status_layanan == 2 ? 'Tidak Aktif' : 'Menolak')) . '</span>'
				: '<span class="badge badge-secondary">Tidak Ada Data</span>')
            ->addColumn('action', function ($sekolah) {
				$btn = '<div style="text-align:center;"><div class="btn-group">';
				$btn .= '<a href="'.url('dashboard/sekolahs/'.Hashids::encode($sekolah->id).'/edit').'" class="btn btn-primary btn-xs btn-icon" title="'.__('general.edit').'" data-toggle="tooltip" data-placement="left"><i class="fa fa-edit"></i></a>';
				$btn .= '<a href="'.url('dashboard/sekolahs/'.Hashids::encode($sekolah->id).'').'" class="btn btn-danger btn-xs btn-icon" data-delete="" title="'.__('general.delete').'" data-toggle="tooltip" data-placement="left"><i class="fa fa-trash"></i></a>';
				$btn .= '</div></div>';
				return $btn;
            })
			->addColumn('control', function ($sekolah) {
				$check = '<div style="text-align:center;"><a href="javascript:void(0);" class="btn btn-secondary btn-xs btn-icon" data-placement="left"><i class="fa fa-plus"></i></a></div>';
				return $check;
			})
			->escapeColumns([])
			->rawColumns(['status_layanan'])
			->make(true);
	}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
		if(Auth::user()->can('create-sekolahs')) {
   			$kecamatans = Wilayah::where('child_id', 0)->pluck('nama_wilayah', 'id');
 
			return view('backend.sekolah.create', [
				'sppg' => null,
				'kecamatans' => $kecamatans
			]);
		} else {
			return redirect('forbidden');
		}
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
	{
		if (!Auth::user()->can('create-sekolahs')) {
			return redirect('forbidden');
		}

		$request->validate([
			'nama' => 'required',
			'wilayah_id' => 'required',
			'jenis_id' => 'required',
			'status' => 'required',
			'jumlah_total' => 'nullable|numeric',
			'alamat' => 'nullable|string'
		]);

		try {
			Sekolah::create([
				'nama' => $request->nama,
				'wilayah_id' => $request->wilayah_id,
				'jenis_id' => $request->jenis_id,
				'status' => $request->status,
				'jumlah_total' => $request->jumlah_total,
				'alamat' => $request->alamat,
				'created_by' => Auth::id(),
				'updated_by' => Auth::id(),
			]);

			return redirect('dashboard/sekolahs')->with('flash_message', 'Data Sekolah berhasil disimpan');

		} catch (\Exception $e) {
			return redirect()->back()
				->with('error_message', 'Terjadi kesalahan')
				->withInput();
		}
	}

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
		if(Auth::user()->can('update-sekolahs')) {
			$ids = Hashids::decode($id);
			$sekolah = sekolah::findOrFail($ids[0]);
			$kecamatans = Wilayah::where('child_id', 0)->pluck('nama_wilayah', 'id');

			return view('backend.sekolah.edit', compact('sekolah', 'kecamatans'));
		} else {
			return redirect('forbidden');
		}
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id)
	{
		if (!Auth::user()->can('update-sekolahs')) {
			return redirect('forbidden')
				->with('error_message', 'Tidak punya akses');
		}

		$ids = Hashids::decode($id);
		$sekolah = Sekolah::findOrFail($ids[0]);

		// VALIDASI
		$request->validate([
			'nama' => 'required|string|max:255',
			'wilayah_id' => 'required',
			'jenis_id' => 'required',
			'status' => 'required',
			'jumlah_total' => 'nullable|numeric',
			'alamat' => 'nullable|string'
		]);

		try {
			// UPDATE DATA
			$sekolah->update([
				'nama' => $request->nama,
				'wilayah_id' => $request->wilayah_id,
				'jenis_id' => $request->jenis_id,
				'status' => $request->status,
				'jumlah_total' => $request->jumlah_total,
				'alamat' => $request->alamat,
				'updated_by' => Auth::id(),
			]);

			return redirect('dashboard/sekolahs')
				->with('flash_message', 'Data sekolah berhasil diupdate');

		} catch (\Exception $e) {
			return redirect()->back()
				->with('error_message', 'Gagal update data')
				->withInput();
		}
	}

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
		if(Auth::user()->can('delete-sekolahs')) {
			$ids = Hashids::decode($id);
			sekolah::destroy($ids[0]);

			return redirect('dashboard/sekolahs')->with('flash_message', 'Data berhasil dihapus');
		} else {
			return redirect('forbidden');
		}
    }
	
	/**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return void
     */
    public function deleteAll(Request $request)
    {
		if(Auth::user()->can('delete-sekolahs')) {
			if ($request->has('id')) {
				$ids = $request->id;
				foreach($ids as $id){
					$idd = Hashids::decode($id);
					sekolah::destroy($idd[0]);
				}
				return redirect('dashboard/sekolahs')->with('flash_message', 'Data berhasil dihapus');
			} else {
				return redirect('dashboard/sekolahs')->with('flash_message', 'Data mu aman, belum dihapus');
			}
		} else {
			return redirect('forbidden');
		}
    }

	public function getSekolah(Request $request)
	{
		try {
			$sekolahs = Sekolah::select('id', 'nama')
				->where('status_layanan', 1) // 🔥 selalu filter status 1
				->when($request->term, function ($q) use ($request) {
					$q->where('nama', 'like', '%' . $request->term . '%');
				})
				->limit(10)
				->get();

			return response()->json([
				'data' => $sekolahs
			]);

		} catch (\Exception $e) {
			\Log::error($e);

			return response()->json([
				'data' => [],
				'error' => $e->getMessage()
			]);
		}
	}
}
