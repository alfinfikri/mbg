<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Infografis;

class InfografisController extends Controller
{

    public function index(Request $request)
    {
        $query = Infografis::select('id','gambar','keterangan','created_at AS tanggal')->orderBy('id', 'DESC')->paginate(6);

        foreach($query as $infografis){
            if($infografis->gambar != null) {
                $infografis->gambar = url('/po-content/uploads/' . $infografis->gambar);
            }
        }

        return response()->json($query);
    }

    public function show($id)
    {
        $info = Infografis::select('id','gambar','keterangan','created_at AS tanggal')->where('id', $id)->first();
        if(!$info){
            return response()->json([
                'message' => 'data infografis tidak ada'
            ], 404);
        }
        if($info->gambar != null) {
            $info->gambar = url('/po-content/uploads/' . $info->gambar);
        }

        return response()->json($info);
    }

}