<?php

namespace App\Http\Controllers;

use App\Models\ImagenProducto;
use App\Models\Producto;
use App\Services\FileService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class InventarioController extends Controller
{
    private $fileService;
    public function __construct(FileService $fileService) {
        $this->fileService = $fileService;
    }
    public function upload_product(Request $request){
        try {

            $usuario = Auth::user();
            $productos = [
                "nombre"=>$request['nombre'],	
                "descripcion"=>$request['descripcion'],	
                "precio"=>$request['precio'],
                "color"=>$request['color']??null,
                "cantidad"=>$request['cantidad'],	
                "estado"=>1,
                "id_usuario"=>$usuario->id,
            ];
            $id_producto = Producto::insertGetId($productos);
            $urlThumbrl = $this->fileService->saveThumblr($id_producto,$request['imagenes'][0])->data;
            Producto::where('id',$id_producto)->update(['url_image'=>$urlThumbrl]);
            $imagenes = [];
            foreach ($request['imagenes'] as  $base64) {
                $guardarImagen = $this->fileService->saveImage($id_producto,$request['nombre'],$base64);
                if(!$guardarImagen->ok) throw new Exception($guardarImagen->message);
                $url = $guardarImagen->data;
                $imagenes[]=[
                    "id_producto"=>$id_producto,
                    "url_image"=>$url,
                    "estado"=>1,
                    "id_usuario"=>$usuario->id,
                    "created_at"=>now(),
                ];
            }
            ImagenProducto::insert($imagenes);
            return response()->json(["ok"=>true],200);
        } catch (Exception $e) {
            return responseErrorController($e);
        }
    }


}
