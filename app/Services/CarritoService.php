<?php

namespace App\Services;

use App\Models\Carrito;
use App\Models\CarritoItems;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class CarritoService
{
    
    public function guardarProductoUsuario($id, $cantidad){
        try {
            $carritoExistente = Carrito::where('id_usuario', Auth::user()->id)->where('estado',1)->first();
            if($carritoExistente){
                $producto_carrito = CarritoItems::where('id_carrito', $carritoExistente->id)->where('id_producto', $id)->first();
                if(!$producto_carrito){
                    CarritoItems::create(['id_carrito' => $producto_carrito->id_carrito, 'id_producto' => $id, 'cantidad' => $cantidad]);
                }else{
                    CarritoItems::where('id', $producto_carrito->id)->update(['cantidad' => (int)$producto_carrito->cantidad + (int)$cantidad]);
                }
            }else{
                $carrito = Carrito::create(['id_usuario' => Auth::user()->id]);
                CarritoItems::create(['id_carrito' => $carrito->id, 'id_producto' => $id, 'cantidad' => $cantidad]);
            }
            return responseSuccessService(true);
        } catch (Exception $e) {
            return responseErrorService($e);
        }
    }

    public function getCarProductUser(){
        try {
            $usuario = Auth::user();
            $data = Carrito::select(
                'carrito_items.id as id_carrito_producto',
                'carrito_items.cantidad as cantidad_producto',
                'carrito_items.estado as estado_carrito_producto',
                'carrito_items.created_at as fecha_carrito_producto',
                'productos.id as id_producto',
                'productos.descripcion as producto',
                'productos.precio',
                'productos.stock',
                'productos.estado as estado_producto',
            )
            ->join('carrito_items', 'carrito.id','carrito_items.id_carrito')
            ->join('productos', 'productos.id','carrito_items.id_producto')
            ->where('carrito.estado',1)
            ->where('carrito.id_usuario',$usuario->id)
            ->get();

            return responseSuccessService($data);
        } catch (Exception $e) {
            return responseErrorService($e);
        }
    }
    public function guardarProductoSession($id, $cantidad){
        try {
            $existeCarrito = Session::has('carrito');
            if(!$existeCarrito) Session::put(['carrito'=>[]]);
            $carrito = Session::get('carrito');
            $id_products = [];
            foreach ($carrito as &$item) {
                if($item['id'] === $id) $item['cantidad'] += $cantidad ;
                $id_products[] = $item['id'];
            }
            if(!in_array($id, $id_products)){
                Session::push('carrito',['id'=>$id, 'cantidad'=>$cantidad]);
            }else{
                Session::put('carrito', $carrito);
            }
            $carritoActualizado = Session::get('carrito');
            return responseSuccessService($carritoActualizado);

        } catch (Exception $e) {
            return responseErrorService($e);
        }
    }
}
