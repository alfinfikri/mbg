<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ApiController extends Controller
{
    public function index()
    {
        $info = [
            'namespace' => 'api/v1',
            'routes' => [
                '/api/v1/post' => [
                    'description' => 'Menampilkan list post',
                    'methods' => [
                        'GET'
                    ],
                ],
                '/api/v1/post/{id}' => [
                    'description' => 'Menampilkan detail post',
                    'methods' => [
                        'GET'
                    ],
                ],
                '/api/v1/infografis' => [
                    'description' => 'Menampilkan list infografis',
                    'methods' => [
                        'GET'
                    ],
                ],
                '/api/v1/infografis/{id}' => [
                    'description' => 'Menampilkan detail infografis',
                    'methods' => [
                        'GET'
                    ],
                ],
                '/api/v1/banner' => [
                    'description' => 'Menampilkan list banner',
                    'methods' => [
                        'GET'
                    ],
                ],
                '/api/v1/banner/{id}' => [
                    'description' => 'Menampilkan detail banner',
                    'methods' => [
                        'GET'
                    ],
                ],
            ]
        ];
        return response()->json($info);
    }
}