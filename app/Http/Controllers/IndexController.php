<?php

namespace App\Http\Controllers;

use App\Models\ImagenProducto;
use App\Models\Producto;
use App\Services\FileService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class IndexController extends Controller
{
    public function get_products(Request $request){
        try {
            $data = Producto::select(
                "id",
                "nombre as name",
                "descripcion as description",
                "url_image as img",
                "precio as price",
                DB::raw("CASE  WHEN cantidad > 0 THEN true ELSE false END as inStock"),
            )->where('estado',1)->get();
            return response()->json(['data'=>$data],200);
        } catch (Exception $e) {
            return responseErrorController($e);
        }
    }


}
