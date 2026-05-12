<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;

use Spatie\Permission\Models\Role;
use App\User;

use Yajra\Datatables\Datatables;
use Vinkla\Hashids\Facades\Hashids;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return void
     */
    public function index(Request $request)
    {
		if(Auth::user()->can('read-users')) {
			return view('backend.users.datatable');
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
		if(Auth::user()->can('read-users')) {
			return view('backend.users.datatable');
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
		if (Auth::user()->hasRole('superadmin')) {
			$users = User::select('users.*');
		} elseif (Auth::user()->hasRole('superadmin 2')) {
			$users = User::select('users.*');
		} elseif (Auth::user()->hasRole('member') || Auth::user()->hasRole('editor')) {
			$users = User::where('users.id', '!=', '1')
				->where('users.id', '=', Auth::user()->id)
				->select('users.*');
        } else {
			$users = User::where('users.id', '!=', '1')
				->select('users.*');
		}
		return Datatables::of($users)
			->addColumn('check', function ($user) {
				$check = '<div style="text-align:center;">
					<input type="checkbox" id="titleCheckdel" />
					<input type="hidden" class="deldata" name="id[]" value="'.Hashids::encode($user->id).'" disabled />
				</div>';
				return $check;
			})
			->addColumn('block', function ($user) {
				return $user->block == 'Y' ? 'Block' : 'Unblock';
			})
            ->addColumn('action', function ($user) {
				$btn = '<div style="text-align:center;"><div class="btn-group">';
				$btn .= '<a href="'.url('dashboard/users/'.Hashids::encode($user->id).'').'" class="btn btn-secondary btn-xs btn-icon" title="'.__('general.view').'" data-toggle="tooltip" data-placement="left"><i class="fa fa-eye"></i></a>';
				$btn .= '<a href="'.url('dashboard/users/'.Hashids::encode($user->id).'/edit').'" class="btn btn-primary btn-xs btn-icon" title="'.__('general.edit').'" data-toggle="tooltip" data-placement="left"><i class="fa fa-edit"></i></a>';
				if ($user->id != '1') {
					if (Auth::user()->hasRole('superadmin') || Auth::user()->hasRole('admin')) {
						if ($user->id != Auth::user()->id) {
							$btn .= '<a href="'.url('dashboard/users/'.Hashids::encode($user->id).'').'" class="btn btn-danger btn-xs btn-icon" data-delete="" title="'.__('general.delete').'" data-toggle="tooltip" data-placement="left"><i class="fa fa-trash"></i></a>';
						}
					}
				}
				$btn .= '</div></div>';
				return $btn;
            })
			->addColumn('control', function ($user) {
				$check = '<div style="text-align:center;"><a href="javascript:void(0);" class="btn btn-secondary btn-xs btn-icon"><i class="fa fa-plus"></i></a></div>';
				return $check;
			})
			->escapeColumns([])
			->make(true);
	}

    /**
     * Show the form for creating a new resource.
     *
     * @return void
     */
    public function create(Request $request)
    {
		if(Auth::user()->can('create-users')) {
			$roles = Role::orderBy('name', 'ASC')->get();
			$sppgs = \App\Sppg::orderBy('nama')->pluck('nama','id');
			$sekolahs = \App\Sekolah::orderBy('nama')->pluck('nama','id');
			return view('backend.users.create', compact('roles', 'sppgs', 'sekolahs'));
		} else {
			return redirect('forbidden');
		}
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return void
     */
    public function store(Request $request)
	{
		if (Auth::user()->can('create-users')) {

			$this->validate($request, [
				'name' => 'required',
				'email' => 'required|string|max:255|email|unique:users',
				'password' => 'required',
				'roles' => 'required'
			]);
 
			if ($request->roles == 'sppg') {
				$request->validate([
					'sppg_id' => 'required|exists:sppgs,id'
				]);
			}

			if ($request->roles == 'sekolah') {
				$request->validate([
					'sekolah_id' => 'required|exists:sekolahs,id'
				]);
			}
 
			$username = str_replace(' ', '', strtolower($request->name)) . rand(100,999);

			// tambahan data
			$request->request->add([
				'username' => $username,
				'picture' => '',
				'block' => 'N',
				'created_by' => Auth::user()->id,
				'updated_by' => Auth::user()->id
			]);
 
			$data = $request->except(['password']);
			$data['password'] = bcrypt($request->password);
 
			if ($request->roles == 'sppg') {
				$data['sppg_id'] = $request->sppg_id;
				$data['sekolah_id'] = null;
			} elseif ($request->roles == 'sekolah') {
				$data['sppg_id'] = null;
				$data['sekolah_id'] = $request->sekolah_id;
			} else {
				$data['sppg_id'] = null;
				$data['sekolah_id'] = null;
			}

			// simpan user
			$user = User::create($data);

			if (Auth::user()->hasRole(['superadmin','admin'])) {
				$user->syncRoles([$request->roles]); // 🔥 pakai array
			}

			// 🔥 buat folder user
			$path1 = str_replace(['\po-includes','/po-includes'], '', base_path('po-content/uploads/users/user-' . $user->id));
			$path2 = str_replace(['\po-includes','/po-includes'], '', base_path('po-content/uploads/medium/users/user-' . $user->id));

			if (!File::isDirectory($path1)) {
				File::makeDirectory($path1, 0777, true, true);
			}

			if (!File::isDirectory($path2)) {
				File::makeDirectory($path2, 0777, true, true);
			}

			return redirect('dashboard/users')->with('flash_message', 'Data User berhasil disimpan');

		} else {
			return redirect('forbidden');
		}
	}

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return void
     */
    public function show($id)
    {
		if(Auth::user()->can('read-users')) {
			$ids = Hashids::decode($id);
			$user = User::findOrFail($ids[0]);
			
			return view('backend.users.show', compact('user'));
		} else {
			return redirect('forbidden');
		}
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return void
     */
    public function edit(Request $request, $id)
    {
		if(Auth::user()->can('update-users')) {
			$ids = Hashids::decode($id);
			
			if (Auth::user()->hasRole('superadmin') || Auth::user()->hasRole('admin')) {
				if (Auth::user()->hasRole('superadmin')) {
					$roles = Role::orderBy('name', 'ASC')->get();
				} else {
					$roles = Role::where('id', '!=', '1')->orderBy('name', 'ASC')->get();
				}

				$user = User::select('id', 'sppg_id', 'sekolah_id','username', 'name', 'email', 'telp', 'block', 'picture')->findOrFail($ids[0]);
				$sppgs = \App\Sppg::orderBy('nama')->pluck('nama','id');
				$sekolahs = \App\Sekolah::orderBy('nama')->pluck('nama','id');
				return view('backend.users.edit', compact('user', 'roles', 'sppgs', 'sekolahs'));
			} else {
				if (Auth::user()->id == $ids[0]) {
					$roles = Role::where('id', '!=', '1')->orderBy('name', 'ASC')->get();

					$user = User::select('id', 'sppg_id', 'sekolah_id', 'username', 'name', 'email', 'telp', 'block', 'picture')->findOrFail($ids[0]);
					$sppgs = \App\Sppg::orderBy('nama')->pluck('nama','id');
					$sekolahs = \App\Sekolah::orderBy('nama')->pluck('nama','id');
					return view('backend.users.edit', compact('user', 'roles', 'sppgs', 'sekolahs'));
				} else {
					return redirect('dashboard/users/'. Hashids::encode(Auth::user()->id) .'/edit');
				}
			}
		} else {
			return redirect('forbidden');
		}
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int      $id
     *
     * @return void
     */
    public function update(Request $request, $id)
	{
		if (!Auth::user()->can('update-users')) {
			return redirect('forbidden');
		}

		$ids = Hashids::decode($id);
		$user = User::findOrFail($ids[0]);

		// VALIDASI
		$this->validate($request, [
			'name' => 'required',
			'email' => 'required|string|max:255|email|unique:users,email,' . $user->id,
		]);

		// 🔥 validasi sppg jika role sppg
		if ($request->has('roles') && $request->roles == 'sppg') {
			$request->validate([
				'sppg_id' => 'required|exists:sppgs,id'
			]);
		}

		if ($request->has('roles') && $request->roles == 'sekolah') {
			$request->validate([
				'sekolah_id' => 'required|exists:sekolahs,id'
			]);
		}

		// tambahan data
		$request->request->add([
			'updated_by' => Auth::user()->id
		]);

		// ambil data
		$data = $request->except(['password']);

		// 🔥 handle password
		if ($request->filled('password')) {
			$data['password'] = bcrypt($request->password);
		}

		// 🔥 handle sppg_id
		if ($request->has('roles')) {
			if ($request->roles == 'sppg') {
				$data['sppg_id'] = $request->sppg_id;
				$data['sekolah_id'] = null;
			} elseif ($request->roles == 'sekolah') {
				$data['sppg_id'] = null;
				$data['sekolah_id'] = $request->sekolah_id;
			} else {
				$data['sppg_id'] = null;
				$data['sekolah_id'] = null;
			}
		}

		// update user
		$user->update($data);

		if (Auth::user()->hasRole(['superadmin','admin']) && $request->has('roles')) {

			$role = Role::where('name', $request->roles)->first();

			if ($role) {
				$user->roles()->sync([$role->id]); // 🔥 FIX PALING AMAN
			}
		}

		// redirect
		if (Auth::user()->hasRole('member')) {
			return redirect('dashboard/users/' . Hashids::encode(Auth::user()->id) . '/edit')
				->with('flash_message', 'User updated!');
		}

		return redirect('dashboard/users')
			->with('flash_message', 'Data User berhasil di Update');
	}

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return void
     */
    public function destroy($id)
    {
		if(Auth::user()->can('delete-users')) {
			$ids = Hashids::decode($id);
			User::destroy($ids[0]);

			return redirect('dashboard/users')->with('flash_message', 'Data User berhasil di Hapus');
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
		if(Auth::user()->can('delete-users')) {
			if ($request->has('id')) {
				$ids = $request->id;
				foreach($ids as $id){
					$idd = Hashids::decode($id);
					User::destroy($idd[0]);
				}
				return redirect('dashboard/users')->with('flash_message', 'Data User berhasil di Hapus');
			} else {
				return redirect('dashboard/users')->with('flash_message', __('user.destroy_error_notif'));
			}
		} else {
			return redirect('forbidden');
		}
    }
	
	public function getUser(Request $request)
	{
		if(Auth::user()->can('read-users')) {
			$term = trim($request->q);

			if (empty($term)) {
				$users = User::select('id', 'name')->where([['id', '>', 1],['block', '=', 'N']])->limit(20)->get();
			} else {
				$users = User::select('id', 'name')->where([['name', 'LIKE', '%'.$term.'%'],['id', '>', 1],['block', '=', 'N']])->get();
			}

			$fusers = [];

			foreach ($users as $user) {
				$fusers[] = ['id' => $user->id, 'text' => $user->name];
			}

			return \Response::json($fusers);
		} else {
			return redirect('forbidden');
		}
	}
	
	public function getUserNotMe(Request $request)
	{
		if(Auth::user()->can('read-users')) {
			$term = trim($request->q);

			if (empty($term)) {
				$users = User::select('id', 'name')->where([['id', '>', 1],['id', '!=', Auth::user()->id],['block', '=', 'N']])->limit(20)->get();
			} else {
				$users = User::select('id', 'name')->where([['name', 'LIKE', '%'.$term.'%'],['id', '>', 1],['id', '!=', Auth::user()->id],['block', '=', 'N']])->get();
			}

			$fusers = [];

			foreach ($users as $user) {
				$fusers[] = ['id' => $user->id, 'text' => $user->name];
			}

			return \Response::json($fusers);
		} else {
			return redirect('forbidden');
		}
	}
}
