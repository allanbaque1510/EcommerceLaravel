<?php

namespace App\Services;

use App\Models\Producto;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\ImageManager;
class ProductService
{

    public function getInfoProductos(Array $id_prductos){
        try {
            $data = Producto::select('id','descripcion','precio','cantidad','estado','nombre','url_image')->whereIn('id', $id_prductos)->get();
            return responseSuccessService($data);
        } catch (Exception $e) {
            return responseErrorService($e);
        }
    }

}