<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Infografis;

use Yajra\Datatables\Datatables;
use Vinkla\Hashids\Facades\Hashids;

class InfografisController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return void
     */
    public function index(Request $request)
    {
		if(Auth::user()->can('read-infografis')) {
			return view('backend.infografis.datatable');
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
		if(Auth::user()->can('read-infografis')) {
			return view('backend.infografis.datatable');
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
		$infografis = Infografis::leftJoin('users', 'users.id', '=', 'infografis.created_by')
					->select('infografis.*', 'users.id as uid', 'users.name as uname')
					->orderBy('infografis.id', 'desc');
		return Datatables::of($infografis)
			->addColumn('check', function ($infografis) {
				$check = '<div style="text-align:center;">
					<input type="checkbox" id="titleCheckdel" />
					<input type="hidden" class="deldata" name="id[]" value="'.Hashids::encode($infografis->id).'" disabled />
				</div>';
				return $check;
			})
            ->addColumn('action', function ($infografis) {
				$btn = '<div style="text-align:center;"><div class="btn-group">';
				$btn .= '<a href="'.url('dashboard/infografis/'.Hashids::encode($infografis->id).'').'" class="btn btn-secondary btn-xs btn-icon" title="'.__('general.view').'" data-toggle="tooltip" data-placement="left"><i class="fa fa-eye"></i></a>';
				$btn .= '<a href="'.url('dashboard/infografis/'.Hashids::encode($infografis->id).'/edit').'" class="btn btn-primary btn-xs btn-icon" title="'.__('general.edit').'" data-toggle="tooltip" data-placement="left"><i class="fa fa-edit"></i></a>';
				$btn .= '<a href="'.url('dashboard/infografis/'.Hashids::encode($infografis->id).'').'" class="btn btn-danger btn-xs btn-icon" data-delete="" title="'.__('general.delete').'" data-toggle="tooltip" data-placement="left"><i class="fa fa-trash"></i></a>';
				$btn .= '</div></div>';
				return $btn;
            })
			->addColumn('control', function ($infografis) {
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
		if(Auth::user()->can('create-infografis')) {
			return view('backend.infografis.create');
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
		if(Auth::user()->can('create-infografis')) {
			$this->validate($request,[
				'gambar'     => 'required',
				'keterangan' => 'required',
				'tanggal'	 => 'required'
			]);

			$request->request->add([
				'created_by' => Auth::User()->id,
				'updated_by' => Auth::User()->id,
				'tanggal' 	 => $request->tanggal
			]);
			$requestData = $request->all();

			Infografis::create($requestData);
			return redirect('dashboard/infografis/table')->with('flash_message', __('infografis.store_notif'));
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
		if(Auth::user()->can('read-infografis')) {
			$ids = Hashids::decode($id);
			$infografis = Infografis::findOrFail($ids[0]);
			return view('backend.infografis.show', compact('infografis'));
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
		if(Auth::user()->can('update-infografis')) {
			$ids = Hashids::decode($id);
			$infografis = Infografis::findOrFail($ids[0]);
			return view('backend.infografis.edit', compact('infografis'));
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
		if(Auth::user()->can('update-infografis')) {
			$ids = Hashids::decode($id);
			
			$this->validate($request,[
				'gambar' => 'required',
				'keterangan' => 'required',
				'tanggal'	 => 'required'
			]);
			
			$request->request->add([
				'updated_by' => Auth::User()->id,
				'tanggal' 	 => $request->tanggal
			]);
			$requestData = $request->all();

			$infografis = Infografis::findOrFail($ids[0]);
			$infografis->update($requestData);

			return redirect('dashboard/infografis/table')->with('flash_message', __('infografis.update_notif'));
		} else {
			return redirect('forbidden');
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
		if(Auth::user()->can('delete-infografis')) {
			$ids = Hashids::decode($id);
			Infografis::destroy($ids[0]);
			return redirect('dashboard/infografis/table')->with('flash_message', __('infografis.destroy_notif'));
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
		if(Auth::user()->can('delete-infografis')) {
			if ($request->has('id')) {
				$ids = $request->id;
				foreach($ids as $id){
					$idd = Hashids::decode($id);
					Infografis::destroy($idd[0]);
				}
				return redirect('dashboard/infografis/table')->with('flash_message', __('infografis.destroy_notif'));
			} else {
				return redirect('dashboard/infografis/table')->with('flash_message', __('infografis.destroy_error_notif'));
			}
		} else {
			return redirect('forbidden');
		}
    }
}