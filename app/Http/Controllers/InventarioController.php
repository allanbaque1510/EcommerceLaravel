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

class InventarioController extends Controller
{
    private $fileService;
    public function __construct(FileService $fileService) {
        $this->fileService = $fileService;
    }
    public function upload_product(Request $request){
        try {
            DB::beginTransaction();
            $arrayImage = [];
            foreach ($request['imagenes'] as $value) {
                $isBase64 = base64_decode(explode(',', $value)[1]);
                if(!$isBase64) throw new Exception("Debe subir un archivo valido");
                $fileMime = finfo_buffer(finfo_open(FILEINFO_MIME_TYPE),$isBase64);
                $infoImage = explode('/',$fileMime);
                if($infoImage[0] != 'image') throw new Exception("Solo se admiten imagenes");
                $object = new \stdClass();
                $object->image = $isBase64;
                $object->extension = $infoImage[1];
                $arrayImage[] = $object;
            }
            
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
            Log::info($productos);
            $id_producto = Producto::insertGetId($productos);
            $urlThumbrl = $this->fileService->saveThumblr($id_producto,$arrayImage[0])->data;
            Producto::where('id',$id_producto)->update(['url_image'=>$urlThumbrl]);
            $imagenes = [];
            foreach ($arrayImage as  $image) {
                $guardarImagen = $this->fileService->saveImage($id_producto,$request['nombre'],$image);
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
            DB::commit();
            return response()->json(["ok"=>true],200);
        } catch (Exception $e) {
            DB::rollBack();
            return responseErrorController($e);
        }
    }


}
