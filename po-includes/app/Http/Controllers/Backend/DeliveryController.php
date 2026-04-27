<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Delivery;
use App\Sppg;
use App\MenuHarian;
use DB;
use Carbon\Carbon;
use Yajra\Datatables\Datatables;
use Vinkla\Hashids\Facades\Hashids;

class DeliveryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return void
     */
    public function index(Request $request)
    {
		if(Auth::user()->can('read-deliverys')) {
			return view('backend.delivery.datatable');
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
		if(Auth::user()->can('read-deliverys')) {
			return view('backend.delivery.datatable');
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
			$deliverys = Delivery::with(['sppg','menu','sekolahs'])
				->when(Auth::user()->sppg_id, fn($q) => $q->where('sppg_id', Auth::user()->sppg_id))
				->orderBy('id', 'desc');
		} else {
            $deliverys = Delivery::with(['sppg','menu','sekolahs'])->orderBy('id', 'desc');
        }

        return Datatables::of($deliverys)

            ->addColumn('check', function ($delivery) {
                return '<div style="text-align:center;">
                    <input type="checkbox" />
                    <input type="hidden" class="deldata" name="id[]" value="'.Hashids::encode($delivery->id).'" disabled />
                </div>';
            })

            ->addColumn('tanggal', function ($s) {
                return \Carbon\Carbon::parse($s->tanggal)->format('d-m-Y');
            })

            ->addColumn('sppg', fn($s) => optional($s->sppg)->nama ?? '-')

            ->addColumn('menu', fn($s) => optional($s->menu)->nama ?? '-')

            ->addColumn('jumlah_porsi', function ($s) {
                $colors = ['primary','success','warning','danger','info'];
                return $s->sekolahs->map(function ($item, $i) use ($colors) {
                    $color = $colors[$i % count($colors)];

                    return '<span class="badge badge-'.$color.' mr-1">'
                        .$item->nama.' ('.$item->pivot->jumlah_porsi.' porsi)</span>';
                })->implode(' ');
            })

            ->addColumn('action', function ($delivery) {
                return '<div style="text-align:center;"><div class="btn-group">
                    <a href="'.url('dashboard/deliverys/'.Hashids::encode($delivery->id).'/edit').'" class="btn btn-primary btn-xs btn-icon">
                        <i class="fa fa-edit"></i>
                    </a>
                    <a href="'.url('dashboard/deliverys/'.Hashids::encode($delivery->id)).'" class="btn btn-danger btn-xs btn-icon" data-delete="">
                        <i class="fa fa-trash"></i>
                    </a>
                </div></div>';
            })

            ->addColumn('control', function () {
                return '<div style="text-align:center;">
                    <a href="javascript:void(0);" class="btn btn-secondary btn-xs btn-icon">
                        <i class="fa fa-plus"></i>
                    </a>
                </div>';
            })

            ->rawColumns(['check','jumlah_porsi','action','control'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
		if(Auth::user()->can('create-deliverys')) {
            $sppgs = Sppg::pluck('nama','id');
            $menus = MenuHarian::whereDate('tanggal', today())
                ->get()
                ->mapWithKeys(function ($item) {
                    return [
                        $item->id => $item->nama . ' (' . Carbon::parse($item->tanggal)->format('d-m-Y') . ')'
                    ];
                });

            return view('backend.delivery.create', compact('sppgs','menus'));

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
        if (Auth::user()->can('create-deliverys')) {

            $this->validate($request, [
                'sppg_id' => 'required|exists:sppgs,id',
                'menu_id' => 'required|exists:menu_harians,id',
                'tanggal' => 'required|date',
                'foto' => 'nullable|string',
                'porsi' => 'required|array'
            ]);

            // simpan delivery
            $delivery = Delivery::create([
                'sppg_id' => $request->sppg_id,
                'menu_id' => $request->menu_id,
                'tanggal' => $request->tanggal,
                'foto' => $request->foto,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            $data = [];

            foreach ($request->porsi ?? [] as $sekolah_id => $jumlah) {
                if ($jumlah > 0) {
                    $data[] = [
                        'delivery_id' => $delivery->id,
                        'sekolah_id' => $sekolah_id,
                        'jumlah_porsi' => $jumlah
                    ];
                }
            }

            DB::table('delivery_sekolahs')->insert($data);

            return redirect('dashboard/deliverys')
                ->with('flash_message', 'Data delivery berhasil disimpan');

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
		if(Auth::user()->can('read-deliverys')) {
			$ids = Hashids::decode($id);
			$sppg = Sppg::findOrFail($ids[0]);

			return view('backend.delivery.show', compact('sppg'));
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
        if (Auth::user()->can('update-deliverys')) {

            $decoded = Hashids::decode($id);
            $id = $decoded[0] ?? null;

            if (!$id) {
                return redirect()->back()->with('error_message', 'ID tidak valid');
            }

            // ambil data delivery + relasi sekolah
            $delivery = Delivery::with('sekolahs')->findOrFail($id);
            $porsi = DB::table('delivery_sekolahs')->where('delivery_id', $delivery->id)->pluck('jumlah_porsi', 'sekolah_id');

            // dropdown
            $sppgs = Sppg::pluck('nama','id');
            $menus = MenuHarian::whereDate('tanggal', today())
                ->get()
                ->mapWithKeys(function ($item) {
                    return [
                        $item->id => $item->nama . ' (' . Carbon::parse($item->tanggal)->format('d-m-Y') . ')'
                    ];
                });

            return view('backend.delivery.edit', compact('delivery','sppgs','menus', 'porsi'));

        } else {
            return redirect('forbidden');
        }
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()->can('update-deliverys')) {

            $decoded = Hashids::decode($id);
            $id = $decoded[0] ?? null;

            if (!$id) {
                return redirect()->back()->with('error_message', 'ID tidak valid');
            }

            $delivery = Delivery::findOrFail($id);

            $this->validate($request, [
                'sppg_id' => 'required|exists:sppgs,id',
                'menu_id' => 'required|exists:menu_harians,id',
                'tanggal' => 'required|date',
                'foto' => 'nullable|string',
                'porsi' => 'required|array'
            ]);

            // update delivery
            $delivery->update([
                'sppg_id' => $request->sppg_id,
                'menu_id' => $request->menu_id,
                'tanggal' => $request->tanggal,
                'foto' => $request->foto,
                'updated_by' => Auth::id(),
            ]);

            // hapus data lama pivot
            DB::table('delivery_sekolahs')
                ->where('delivery_id', $delivery->id)
                ->delete();

            $total = array_sum($request->porsi);

            if ($total <= 0) {
                return back()->with('error_message', 'Total porsi harus diisi');
            }
            
            $data = [];

            foreach ($request->porsi ?? [] as $sekolah_id => $jumlah) {
                if ($jumlah > 0) {
                    $data[] = [
                        'delivery_id' => $delivery->id,
                        'sekolah_id' => $sekolah_id,
                        'jumlah_porsi' => $jumlah
                    ];
                }
            }

            DB::table('delivery_sekolahs')->insert($data);

            return redirect('dashboard/deliverys')->with('flash_message', 'Data delivery berhasil diupdate');

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
		if(Auth::user()->can('delete-deliverys')) {
			$ids = Hashids::decode($id);
			Delivery::destroy($ids[0]);

			return redirect('dashboard/deliverys')->with('flash_message', 'Data berhasil dihapus');
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
		if(Auth::user()->can('delete-deliverys')) {
			if ($request->has('id')) {
				$ids = $request->id;
				foreach($ids as $id){
					$idd = Hashids::decode($id);
					Delivery::destroy($idd[0]);
				}
				return redirect('dashboard/deliverys')->with('flash_message', 'Data berhasil dihapus');
			} else {
				return redirect('dashboard/deliverys')->with('flash_message', 'Data mu aman, belum dihapus');
			}
		} else {
			return redirect('forbidden');
		}
    }

    public function getSekolahBySppg($id)
    {
        $sekolahs = \DB::table('sppg_sekolahs')
            ->join('sekolahs', 'sekolahs.id', '=', 'sppg_sekolahs.sekolah_id')
            ->where('sppg_sekolahs.sppg_id', $id)
            ->select('sekolahs.id', 'sekolahs.nama')
            ->get();

        return response()->json($sekolahs);
    }
}
