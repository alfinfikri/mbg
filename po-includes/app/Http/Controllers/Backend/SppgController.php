<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Sppg;
use App\Wilayah;
use DB;
use Yajra\Datatables\Datatables;
use Vinkla\Hashids\Facades\Hashids;

class SppgController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return void
     */
    public function index(Request $request)
    {
		if(Auth::user()->can('read-sppgs')) {
			return view('backend.sppg.datatable');
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
		if(Auth::user()->can('read-sppgs')) {
			return view('backend.sppg.datatable');
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
		$sppgs = Sppg::with(['wilayah.parent', 'sekolahs'])->orderBy('id', 'desc');

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
			->addColumn('sekolah', function ($s) {
				return $s->sekolahs->map(function ($item) {
					return '• ' . $item->nama;
				})->implode('<br>');
			})
            ->addColumn('action', function ($sppg) {
				$btn = '<div style="text-align:center;"><div class="btn-group">';
				$btn .= '<a href="'.url('dashboard/sppgs/'.Hashids::encode($sppg->id).'/edit').'" class="btn btn-primary btn-xs btn-icon" title="'.__('general.edit').'" data-toggle="tooltip" data-placement="left"><i class="fa fa-edit"></i></a>';
				$btn .= '<a href="'.url('dashboard/sppgs/'.Hashids::encode($sppg->id).'').'" class="btn btn-danger btn-xs btn-icon" data-delete="" title="'.__('general.delete').'" data-toggle="tooltip" data-placement="left"><i class="fa fa-trash"></i></a>';
				$btn .= '</div></div>';
				return $btn;
            })
			->addColumn('control', function ($sppg) {
				$check = '<div style="text-align:center;"><a href="javascript:void(0);" class="btn btn-secondary btn-xs btn-icon" data-placement="left"><i class="fa fa-plus"></i></a></div>';
				return $check;
			})
			->escapeColumns([])
			->make(true);
	}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
		if(Auth::user()->can('create-sppgs')) {
   			$kecamatans = Wilayah::where('child_id', 0)->pluck('nama_wilayah', 'id');
 
			return view('backend.sppg.create', [
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
		if (!Auth::user()->can('create-sppgs')) {
			return redirect('forbidden');
		}

		$request->validate([
			'nama' => 'required',
			'wilayah_id' => 'required',
			'sekolah_id' => 'required|array',
			'sekolah_id.*' => 'exists:sekolahs,id',
			'alamat' => 'nullable|string'
		]);

		try {
			DB::beginTransaction();

			// 1. simpan ke tabel sppgs
			$sppg = Sppg::create([
				'nama' => $request->nama,
				'wilayah_id' => $request->wilayah_id,
				'alamat' => $request->alamat,
				'created_by' => Auth::id(),
				'updated_by' => Auth::id(),
			]);

			// 2. simpan ke pivot sppg_sekolahs
			$sppg->sekolahs()->sync($request->sekolah_id);

			DB::commit();

			return redirect('dashboard/sppgs')
				->with('flash_message', 'Data sppg berhasil disimpan');
				
		} catch (\Exception $e) {
			DB::rollBack();

			return redirect()->back()
				->with('error_message', $e->getMessage())
				->withInput();
		}
	}

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
		if(Auth::user()->can('read-sppgs')) {
			$ids = Hashids::decode($id);
			$sppg = Sppg::findOrFail($ids[0]);

			return view('backend.sppg.show', compact('sppg'));
		} else {
			return redirect('forbidden');
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
		if(Auth::user()->can('update-sppgs')) {
			$ids = Hashids::decode($id);
			$sppg = Sppg::findOrFail($ids[0]);
			$kecamatans = Wilayah::where('child_id', 0)->pluck('nama_wilayah', 'id');

			return view('backend.sppg.edit', compact('sppg', 'kecamatans'));
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
		if (!Auth::user()->can('update-sppgs')) {
			return redirect('forbidden')
				->with('error_message', 'Tidak punya akses');
		}

		// decode hashid aman
		$decoded = Hashids::decode($id);
		$id = $decoded[0] ?? null;

		if (!$id) {
			return redirect()->back()
				->with('error_message', 'ID tidak valid');
		}

		$sppg = Sppg::findOrFail($id);

		// VALIDASI
		$request->validate([
			'nama' => 'required|string|max:255',
			'wilayah_id' => 'required',
			'sekolah_id' => 'required|array',
			'sekolah_id.*' => 'exists:sekolahs,id',
			'alamat' => 'nullable|string'
		]);

		try {
			DB::beginTransaction();

			// 1. update sppg
			$sppg->update([
				'nama' => $request->nama,
				'wilayah_id' => $request->wilayah_id,
				'alamat' => $request->alamat,
				'updated_by' => Auth::id(),
			]);

			// 2. update relasi sekolah (pivot)
			$sppg->sekolahs()->sync($request->sekolah_id);

			DB::commit();

			return redirect('dashboard/sppgs')
				->with('flash_message', 'Data sppg berhasil diupdate');

		} catch (\Exception $e) {
			DB::rollBack();

			return redirect()->back()
				->with('error_message', $e->getMessage())
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
		if(Auth::user()->can('delete-sppgs')) {
			$ids = Hashids::decode($id);
			Sppg::destroy($ids[0]);

			return redirect('dashboard/sppgs')->with('flash_message', 'Data berhasil dihapus');
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
		if(Auth::user()->can('delete-sppgs')) {
			if ($request->has('id')) {
				$ids = $request->id;
				foreach($ids as $id){
					$idd = Hashids::decode($id);
					Sppg::destroy($idd[0]);
				}
				return redirect('dashboard/sppgs')->with('flash_message', 'Data berhasil dihapus');
			} else {
				return redirect('dashboard/sppgs')->with('flash_message', 'Data mu aman, belum dihapus');
			}
		} else {
			return redirect('forbidden');
		}
    }
}
