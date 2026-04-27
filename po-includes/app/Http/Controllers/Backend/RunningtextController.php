<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Runningtext;

use Yajra\Datatables\Datatables;
use Vinkla\Hashids\Facades\Hashids;

class RunningtextController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return void
     */
    public function index(Request $request)
    {
		if(Auth::user()->can('read-runningtext')) {
			return view('backend.runningtext.datatable');
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
		if(Auth::user()->can('read-runningtext')) {
			return view('backend.runningtext.datatable');
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
		$runningtext = Runningtext::leftJoin('users', 'users.id', '=', 'runningtext.created_by')
					   ->select('runningtext.id', 'runningtext.isitext','runningtext.created_by','users.name')
					   ->orderBy('runningtext.id', 'desc');
		return Datatables::of($runningtext)
			->addColumn('check', function ($runningtext) {
				$check = '<div style="text-align:center;">
					<input type="checkbox" id="titleCheckdel" />
					<input type="hidden" class="deldata" name="id[]" value="'.Hashids::encode($runningtext->id).'" disabled />
				</div>';
				return $check;
			})
            ->addColumn('action', function ($runningtext) {
				$btn = '<div style="text-align:center;"><div class="btn-group">';
				$btn .= '<a href="'.url('dashboard/runningtext/'.Hashids::encode($runningtext->id).'').'" class="btn btn-secondary btn-xs btn-icon" title="'.__('general.view').'" data-toggle="tooltip" data-placement="left"><i class="fa fa-eye"></i></a>';
				$btn .= '<a href="'.url('dashboard/runningtext/'.Hashids::encode($runningtext->id).'/edit').'" class="btn btn-primary btn-xs btn-icon" title="'.__('general.edit').'" data-toggle="tooltip" data-placement="left"><i class="fa fa-edit"></i></a>';
				$btn .= '<a href="'.url('dashboard/runningtext/'.Hashids::encode($runningtext->id).'').'" class="btn btn-danger btn-xs btn-icon" data-delete="" title="'.__('general.delete').'" data-toggle="tooltip" data-placement="left"><i class="fa fa-trash"></i></a>';
				$btn .= '</div></div>';
				return $btn;
            })
			->addColumn('control', function ($runningtext) {
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
		if(Auth::user()->can('create-runningtext')) {
			return view('backend.runningtext.create');
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
		if(Auth::user()->can('create-runningtext')) {
			$this->validate($request,[
				'isitext' => 'required'
			]);

			try{
			$cek_runtext = Runningtext::select('id','isitext')->count();
			if($cek_runtext >= 1){
				return redirect('dashboard/runningtext/table')->with('flash_message', 'Running Text Tidak Boleh lebih dari 1, karena sudah automatis selisih 7 hari');
			}else{
				Runningtext::create([
					'isitext' 	 => $request->isitext,
					'created_by' => Auth::User()->id,
					'updated_by' => Auth::User()->id
				]);

				return redirect('dashboard/runningtext/table')->with('flash_message', __('runningtext.store_notif'));
			}
		   }catch (\Exception $e) {
			return redirect()->back()->with(['error' => $e->getMessage()]);
		   }
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
		if(Auth::user()->can('read-runningtext')) {
			$ids = Hashids::decode($id);
			$runningtext = Runningtext::findOrFail($ids[0]);

			return view('backend.runningtext.show', compact('runningtext'));
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
		if(Auth::user()->can('update-runningtext')) {
			$ids = Hashids::decode($id);
			$runningtext = Runningtext::findOrFail($ids[0]);

			return view('backend.runningtext.edit', compact('runningtext'));
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
		if(Auth::user()->can('update-runningtext')) {
			$ids = Hashids::decode($id);
			$this->validate($request,[
				'isitext' => 'required'
			]);

			$request->request->add([
				'updated_by' => Auth::User()->id
			]);
			$requestData = $request->all();

			$runningtext = Runningtext::findOrFail($ids[0]);
			$runningtext->update($requestData);

			return redirect('dashboard/runningtext/table')->with('flash_message', __('runningtext.update_notif'));
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
		if(Auth::user()->can('delete-runningtext')) {
			$ids = Hashids::decode($id);
			Runningtext::destroy($ids[0]);

			return redirect('dashboard/runningtext')->with('flash_message', __('runningtext.destroy_notif'));
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
		if(Auth::user()->can('delete-runningtext')) {
			if ($request->has('id')) {
				$ids = $request->id;
				foreach($ids as $id){
					$idd = Hashids::decode($id);
					Runningtext::destroy($idd[0]);
				}
				return redirect('dashboard/runningtext')->with('flash_message', __('runningtext.destroy_notif'));
			} else {
				return redirect('dashboard/runningtext')->with('flash_message', __('runningtext.destroy_error_notif'));
			}
		} else {
			return redirect('forbidden');
		}
    }
}
