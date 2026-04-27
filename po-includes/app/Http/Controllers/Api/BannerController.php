<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Banner;

class BannerController extends Controller
{

    public function index(Request $request)
    {
        $query = Banner::select('id','gambar','keterangan','created_at AS tanggal')->orderBy('id', 'DESC')->paginate(6);

        foreach($query as $ban){
            if($ban->gambar != null) {
                $ban->gambar = url('/po-content/uploads/' . $ban->gambar);
            }
        }

        return response()->json($query);
    }

    public function show($id)
    {
        $ban = Banner::select('id','gambar','keterangan','created_at AS tanggal')->where('id', $id)->first();
        if(!$ban){
            return response()->json([
                'message' => 'data Banner tidak ada'
            ], 404);
        }
        if($ban->gambar != null) {
            $ban->gambar = url('/po-content/uploads/' . $ban->gambar);
        }

        return response()->json($ban);
    }

}