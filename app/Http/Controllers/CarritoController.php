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
            $isLogin =  Auth::check();
            if($isLogin) {
                $saveProdUser = $this->carritoService->guardarProductoUsuario($request['id'], $request['cantidad']);
                if(!$saveProdUser->ok) throw new Exception($saveProdUser->message);
            }
            
            $saveProductSession = $this->carritoService->guardarProductoSession($request['id'],$request['cantidad']);
            if(!$saveProductSession->ok) throw new Exception($saveProductSession->message);
            
            
        } catch (Exception $e) {
            return responseErrorController($e);
        }
    }


}
