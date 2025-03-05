<?php

namespace App\Http\Controllers;

use App\Services\CarritoService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CarritoController extends Controller
{
    private $carritoService;
    public function __construct(CarritoService $carritoService) {
        $this->carritoService = $carritoService;
    }
    public function saveProductsCarrito(Request $request){
        try {
            $request->validate([
                'id' => 'required|integer',
                'cantidad' => 'required|integer',
            ],
            [
                'id.integer' => 'Error al ingresar el producto',
                'cantidad.integer' => 'Error al ingresar el producto en el carrito',
            ]);
            $isLogin =  Auth::check();
            if($isLogin) {
                $saveProdUser = $this->carritoService->guardarProductoUsuario($request['id'], $request['cantidad']);
                if(!$saveProdUser->ok) throw new Exception($saveProdUser->message);
            }else{
                $saveProductSession = $this->carritoService->guardarProductoSession($request['id'],$request['cantidad']);
                if(!$saveProductSession->ok) throw new Exception($saveProductSession->message);
            }
            return response()->json(['ok'=>true],200);            
        } catch (Exception $e) {
            return responseErrorController($e);
        }
    }


}
