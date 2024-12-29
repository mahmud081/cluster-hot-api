<?php
namespace App\Http\Controllers;

//use Facade\FlareClient\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductController extends Controller
{
    public function show()
    {
        return 'Many Products';
    }

    public function save()
    {
        if (auth()->check()) {
            return response([
                'Product saved'
            ], 200);
        } else {
            return response([
                'Unauthenticated'
            ], 401);
        }
    }

    public function showData(Request $request)
    {
        return response()->json([
            'devices' => $request->devices,
        ]);
    }
}
