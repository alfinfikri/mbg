<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Aduan;
use App\User;
use DB;

use Yajra\Datatables\Datatables;
use Vinkla\Hashids\Facades\Hashids;

class AduanController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return void
     */
    public function index(Request $request)
    {
		if(Auth::user()->can('read-aduans')) {
			return view('backend.aduan.datatable');
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
		if(Auth::user()->can('read-aduans')) {
			return view('backend.aduan.datatable');
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
        $isAdmin = DB::table('model_has_roles')
            ->where('model_id', Auth::id())
            ->where('role_id', 1)
            ->exists();

        if ($isAdmin) {
            $aduans = Aduan::orderBy('id', 'desc')->get();
        } else {
            $aduans = Aduan::where('user_id', Auth::id())
                ->orderBy('id', 'desc')
                ->get();
        }

        return Datatables::of($aduans)

            ->addColumn('tanggal', function ($s) {
                return \Carbon\Carbon::parse($s->tgl_aduan)->format('d-m-Y');
            })
            ->addColumn('nama', fn($s) => $s->kode_tiket)

            ->addColumn('nama', fn($s) => $s->nama)

            ->addColumn('no_hp', fn($s) => $s->no_hp)

            ->addColumn('isi_aduan', fn($s) => $s->isi_aduan)

            ->addColumn('status', function ($s) {
                $map = [
                    0 => '<span class="badge badge-secondary">Menunggu</span>',
                    1 => '<span class="badge badge-warning">Disposisi</span>',
                    2 => '<span class="badge badge-warning">Diproses</span>',
                    3 => '<span class="badge badge-success">Selesai</span>',
                ];
                return $map[$s->status] ?? '-';
            })
            ->addColumn('action', function ($post) {
				$btn = '<div style="text-align:center;"><div class="btn-group">';
				$btn .= '<a href="'.url('dashboard/aduans/'.Hashids::encode($post->id).'').'" class="btn btn-secondary btn-xs btn-icon" title="'.__('general.view').'" data-toggle="tooltip" data-placement="left"><i class="fa fa-eye"></i></a>';
				$btn .= '</div></div>';
				return $btn;
            })

            ->addColumn('control', function () {
                return '';
            })

            ->rawColumns(['status','action'])
            ->make(true);
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
		if(Auth::user()->can('read-aduans')) {
			$ids = Hashids::decode($id);
			$aduan = Aduan::findOrFail($ids[0]);
            $users = User::join('model_has_roles', function ($join) {
                    $join->on('users.id', '=', 'model_has_roles.model_id')
                        ->where('model_has_roles.model_type', 'App\\User');
                })
                ->where('model_has_roles.role_id', 2)
                ->select('users.*')
                ->distinct()
                ->get();
            $isAdmin = DB::table('model_has_roles')
                ->where('model_id', Auth::id())
                ->where('role_id', 1)
                ->exists();

			return view('backend.aduan.show', compact('aduan', 'users', 'isAdmin'));
		} else {
			return redirect('forbidden');
		}
    }

    public function prosesAduan(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $aduan = Aduan::findOrFail($id);

        $aduan->update([
            'user_id' => $request->user_id,
            'tgl_disposisi' => $request->tgl_disposisi,
            'status' => 1
        ]);

        activity()->performedOn($aduan)
				->withProperties([
					'keterangan' => 'Admin pusat sedang berkoordinasi dengan satgas terkait aduan',
					'kode_tiket' => $aduan->kode_tiket
				])
        		->log('Aduan Di Disposisikan ke Satgas');


        return redirect('dashboard/aduans/table')->with('flash_message', 'Aduan berhasil diproses & didisposisi');
    }

    public function responAduan(Request $request, $id)
    {
        $request->validate([
            'respon' => 'required'
        ]);

        $aduan = Aduan::findOrFail($id);

        // pastikan hanya user tujuan yang bisa isi
        if (auth()->id() != $aduan->user_id) {
            abort(403);
        }

        $aduan->update([
            'tgl_proses' => $request->tgl_proses,
            'respon_proses' => $request->respon,
            'status' => 2
        ]);

        activity()->performedOn($aduan)
            ->withProperties([
                'keterangan' => $request->respon,
                'kode_tiket' => $aduan->kode_tiket
            ])
            ->log('Aduan Direspon oleh Satgas');

        return back()->with('success', 'Respon berhasil dikirim');
    }

    public function responAkhir(Request $request, $id)
    {
        $request->validate([
            'respon_selesai' => 'required'
        ]);

        $aduan = Aduan::findOrFail($id);

        // hanya user tujuan
        if (auth()->id() != $aduan->user_id) {
            abort(403);
        }

        $aduan->update([
            'tgl_selesai' => $request->tgl_selesai,
            'respon_selesai' => $request->respon_selesai,
            'status' => 3
        ]);

        activity()->performedOn($aduan)
            ->withProperties([
                'keterangan' => $request->respon_selesai,
                'kode_tiket' => $aduan->kode_tiket
            ])
            ->log('Aduan Diselesaikan oleh Satgas');

        return back()->with('success', 'Aduan selesai');
    }
}