<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\ViewPage;

use Yajra\Datatables\Datatables;
use Vinkla\Hashids\Facades\Hashids;
use Carbon\Carbon;
use PDF;
use Elibyy\TCPDF\Facades\TCPDF;

class ViewPageController extends Controller
{

    public function index(Request $request)
    {
		if(Auth::user()->can('read-viewpage')) {
			return view('backend.viewpage.datatable');
		} else {
			return redirect('forbidden');
		}
    }
	
    public function getIndex(Request $request)
	{
		if(Auth::user()->can('read-viewpage')) {
			return view('backend.viewpage.datatable');
		} else {
			return redirect('forbidden');
		}
	}
	
	public function anyData()
	{
		if(Auth::user()->can('read-viewpage')){
			$viewpage = ViewPage::select('id','device','platform','browser','ip','created_at','updated_at')
				    ->orderBy('created_at', 'DESC')
				    ->limit('2000')
				    ->get();
			return Datatables::of($viewpage)

			->addColumn('dates', function ($viewpage) {
				$dates = Carbon::parse($viewpage->created_at)->translatedFormat('l, d F Y');
				return $dates;
			})
			->addColumn('update', function ($viewpage) {
				$update = Carbon::parse($viewpage->updated_at)->translatedFormat('l, d F Y, H:i:s');
				return $update;
			})
			->addColumn('check', function ($viewpage) {
				$check = '<div style="text-align:center;">
					<input type="checkbox" id="titleCheckdel" />
					<input type="hidden" class="deldata" name="id[]" value="'.Hashids::encode($viewpage->id).'" disabled />
				</div>';
				return $check;
			})
            ->addColumn('action', function ($viewpage) {
				$btn = '<div style="text-align:center;"><div class="btn-group">';
				$btn .= '<a href="'.url('dashboard/viewpage/'.Hashids::encode($viewpage->id).'').'" class="btn btn-secondary btn-xs btn-icon" title="'.__('general.view').'" data-toggle="tooltip" data-placement="left"><i class="fa fa-eye"></i></a>';
				$btn .= '<a href="'.url('dashboard/viewpage/'.Hashids::encode($viewpage->id).'').'" class="btn btn-danger btn-xs btn-icon" data-delete="" title="'.__('general.delete').'" data-toggle="tooltip" data-placement="left"><i class="fa fa-trash"></i></a>';
				$btn .= '</div></div>';
				return $btn;
            })
			->addColumn('control', function ($viewpage) {
				$check = '<div style="text-align:center;"><a href="javascript:void(0);" class="btn btn-secondary btn-xs btn-icon" data-placement="left"><i class="fa fa-plus"></i></a></div>';
				return $check;
			})
			->escapeColumns([])
			->make(true);
		}else{
			return redirect('forbidden');
		}
	}

    public function show($id)
    {
		if(Auth::user()->can('read-viewpage')) {
			$ids = Hashids::decode($id);
			$viewpage = ViewPage::findOrFail($ids[0]);
			return view('backend.viewpage.show', compact('viewpage'));
		} else {
			return redirect('forbidden');
		}
    }

	//cetak
	public function cetak_viewpage()
	{
		if(Auth::user()->can('read-viewpage')){
			return view('backend.viewpage.cetak');
		}else{
		   return redirect('forbidden');
		}
	}

	//cetak perbulan viewpage
	public function cetak_viewpage_aksi(Request $request)
	{
		if(Auth::user()->can('read-viewpage')){
		$start_date = Carbon::parse(request()->start_date)->toDateTimeString();
		$end_date   = Carbon::parse(request()->end_date)->toDateTimeString();

		$cetak = ViewPage::select('id','url','useragent','device','platform','browser','ip','created_at','updated_at')
		         ->whereBetween('created_at',[$start_date,$end_date])
		         ->orderBy('id','ASC')
		         ->limit('6500')
		         ->get();

		$pdf  = new TCPDF;
		$html = view()->make('backend.viewpage.cetak_all', compact('cetak'))->render();
	        $pdf::SetTitle('Cetak Halaman Pengunjung CMSMadani Serangkota');
        	$pdf::AddPage('L','F4');
		$pdf::writeHTML($html, true, false, true, false, '');
		$pdf::Output('Cetak-laporan-halaman-pengunjung.pdf');
		return $html;
		}else{
		   return redirect('forbidden');
		}
	}

    public function destroy($id)
    {
		if(Auth::user()->can('delete-viewpage')) {
			$ids = Hashids::decode($id);
			ViewPage::destroy($ids[0]);

			return redirect('dashboard/viewpage')->with('flash_message', __('viewpage.destroy_notif'));
		} else {
			return redirect('forbidden');
		}
    }
	
    public function deleteAll(Request $request)
    {
		if(Auth::user()->can('delete-viewpage')) {
			if ($request->has('id')) {
				$ids = $request->id;
				foreach($ids as $id){
					$idd = Hashids::decode($id);
					ViewPage::destroy($idd[0]);
				}
				return redirect('dashboard/viewpage')->with('flash_message', __('viewpage.destroy_notif'));
			} else {
				return redirect('dashboard/viewpage')->with('flash_message', __('viewpage.destroy_error_notif'));
			}
		} else {
			return redirect('forbidden');
		}
    }
}