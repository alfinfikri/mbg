<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\MenuHarian;
use App\Sppg;
use Carbon\Carbon;
use DB;
use Yajra\Datatables\Datatables;
use Vinkla\Hashids\Facades\Hashids;

class MenuharianController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return void
     */
	public $path;

	public function __construct()
	{
		//DEFINISIKAN PATH
		$this->path = storage_path('app/public');
	}

	function uploadFile($request, $field, $prefix) 
	{
		if ($request->hasFile($field)) {
			$file = $request->file($field);
			$nama_file = Carbon::now()->timestamp . "_{$prefix}_" . uniqid() . '.' .
						str_replace(' ', '-', $file->getClientOriginalExtension());
			$file->move($this->path, $nama_file);
			return $nama_file;
		}
		return null;
	}

    public function index(Request $request)
    {
		if(Auth::user()->can('read-menuharians')) {
			return view('backend.menuharian.datatable');
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
		if(Auth::user()->can('read-menuharians')) {
			return view('backend.menuharian.datatable');
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
		if (Auth::user()->sppg_id) {
			$menuharians = MenuHarian::with('sppg')
				->when(Auth::user()->sppg_id, fn($q) => $q->where('sppg_id', Auth::user()->sppg_id))
				->orderBy('id', 'desc');
		} else {
			$menuharians = MenuHarian::with('sppg')
				->orderBy('id', 'desc');
		}

		return Datatables::of($menuharians)
			->addColumn('check', function ($menuharian) {
				return '<div style="text-align:center;">
					<input type="checkbox" />
					<input type="hidden" class="deldata" name="id[]" value="'.Hashids::encode($menuharian->id).'" disabled />
				</div>';
			})
			->addColumn('sppg', function ($s) {
				return optional($s->sppg)->nama ?? '-';
			})
			->addColumn('tanggal', function ($s) {
				return \Carbon\Carbon::parse($s->tanggal)->format('d-m-Y');
			})
			->addColumn('nama', fn($s) => $s->nama)
			->addColumn('deskripsi', fn($s) => $s->deskripsi)
            ->addColumn('action', function ($menuharian) {
				$btn = '<div style="text-align:center;"><div class="btn-group">';
				$btn .= '<a href="'.url('dashboard/menuharians/'.Hashids::encode($menuharian->id).'/edit').'" class="btn btn-primary btn-xs btn-icon" title="'.__('general.edit').'" data-toggle="tooltip" data-placement="left"><i class="fa fa-edit"></i></a>';
				$btn .= '<a href="'.url('dashboard/menuharians/'.Hashids::encode($menuharian->id).'').'" class="btn btn-danger btn-xs btn-icon" data-delete="" title="'.__('general.delete').'" data-toggle="tooltip" data-placement="left"><i class="fa fa-trash"></i></a>';
				$btn .= '</div></div>';
				return $btn;
            })
			->addColumn('control', function ($menuharian) {
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
		if(Auth::user()->can('create-menuharians')) { 	
			$sppgs = Sppg::pluck('nama', 'id');

			return view('backend.menuharian.create', [
				'menuharian' => null,
				'sppgs' => $sppgs
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
		if (Auth::user()->can('create-menuharians')) {

			$this->validate($request, [
				'tanggal' => 'required|date',
				'nama' => 'required|string|max:255',
				'sppg_id' => 'required|exists:sppgs,id',
				'deskripsi' => 'nullable|string',
				'foto' => 'required|string',

				'kecil_energi' => 'nullable|numeric',
				'besar_energi' => 'nullable|numeric',
				'kecil_lemak' => 'nullable|numeric',
				'besar_lemak' => 'nullable|numeric',
				'kecil_protein' => 'nullable|numeric',
				'besar_protein' => 'nullable|numeric',
				'kecil_karbohidrat' => 'nullable|numeric',
				'besar_karbohidrat' => 'nullable|numeric',
				'kecil_serat' => 'nullable|numeric',
				'besar_serat' => 'nullable|numeric',
			]);

			// tambahan field
			$request->request->add([
				'created_by' => Auth::user()->id,
				'updated_by' => Auth::user()->id,
			]);

			$requestData = $request->all();

			MenuHarian::create($requestData);

			return redirect('dashboard/menuharians')
				->with('flash_message', 'Menu harian berhasil disimpan');

		} else {
			return redirect('forbidden');
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
		if(Auth::user()->can('read-menuharians')) {
			$ids = Hashids::decode($id);
			$menuharian = MenuHarian::findOrFail($ids[0]);

			return view('backend.menuharian.show', compact('menuharian'));
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
		if (!Auth::user()->can('update-menuharians')) {
			return redirect('forbidden')
				->with('error_message', 'Tidak punya akses');
		}

		// decode hashid aman
		$decoded = Hashids::decode($id);
		$id = $decoded[0] ?? null;

		if (!$id) {
			return redirect()->back()->with('error_message', 'ID tidak valid');
		}

		// ambil data + relasi (optional tapi bagus)
		$menuharian = MenuHarian::findOrFail($id);

		// dropdown sppg
		$sppgs = Sppg::pluck('nama', 'id');

		return view('backend.menuharian.edit', compact('menuharian', 'sppgs'));
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
		if (!Auth::user()->can('update-menuharians')) {
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

		$menuharian = MenuHarian::findOrFail($id);

		// VALIDASI
		$request->validate([
			'tanggal' => 'required|date',
			'nama' => 'required|string|max:255',
			'sppg_id' => 'required|exists:sppgs,id',
			'deskripsi' => 'nullable|string',
			'foto' => 'required|string',
			'kecil_energi' => 'nullable|numeric',
			'besar_energi' => 'nullable|numeric',
			'kecil_lemak' => 'nullable|numeric',
			'besar_lemak' => 'nullable|numeric',
			'kecil_protein' => 'nullable|numeric',
			'besar_protein' => 'nullable|numeric',
			'kecil_karbohidrat' => 'nullable|numeric',
			'besar_karbohidrat' => 'nullable|numeric',
			'kecil_serat' => 'nullable|numeric',
			'besar_serat' => 'nullable|numeric'
		]);

		try {
			DB::beginTransaction();

			// 🔥 update data
			$menuharian->update([
				'tanggal' => $request->tanggal,
				'nama' => $request->nama,
				'sppg_id' => $request->sppg_id,
				'deskripsi' => $request->deskripsi,

				'kecil_energi' => $request->kecil_energi,
				'kecil_lemak' => $request->kecil_lemak,
				'kecil_protein' => $request->kecil_protein,
				'kecil_karbohidrat' => $request->kecil_karbohidrat,
				'kecil_serat' => $request->kecil_serat,

				'besar_energi' => $request->besar_energi,
				'besar_lemak' => $request->besar_lemak,
				'besar_protein' => $request->besar_protein,
				'besar_karbohidrat' => $request->besar_karbohidrat,
				'besar_serat' => $request->besar_serat,

				'foto' => $request->foto,
				'updated_by' => Auth::id(),
			]);

			DB::commit();

			return redirect('dashboard/menuharians')
				->with('flash_message', 'Menu harian berhasil diupdate');

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
		if(Auth::user()->can('delete-menuharians')) {
			$ids = Hashids::decode($id);
			MenuHarian::destroy($ids[0]);

			return redirect('dashboard/menuharians')->with('flash_message', 'Data berhasil dihapus');
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
		if(Auth::user()->can('delete-menuharians')) {
			if ($request->has('id')) {
				$ids = $request->id;
				foreach($ids as $id){
					$idd = Hashids::decode($id);
					MenuHarian::destroy($idd[0]);
				}
				return redirect('dashboard/menuharians')->with('flash_message', 'Data berhasil dihapus');
			} else {
				return redirect('dashboard/menuharians')->with('flash_message', 'Data mu aman, belum dihapus');
			}
		} else {
			return redirect('forbidden');
		}
    }
}
