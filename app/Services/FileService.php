<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\ImageManager;
class FileService
{
    public function saveImage($id_producto, $nombre,$image){
        try {
            $indexImage = count(Storage::disk('public')->allFiles("images/".$id_producto));
            $extension = $image->extension;
            $imageData = $image->image;
            // Inicializar ImageManager con GdDriver
            $manager = new ImageManager(new GdDriver());
            $image = $manager->read($imageData);

            // Redimensionar la imagen manteniendo proporción
            $image = $image->scale(width: 800);

            switch ($extension) {
                case 'jpeg':
                case 'jpg':
                    $encoded = $image->toJpeg(70); // Reducimos calidad a 70%
                    break;
                case 'png':
                    $encoded = $image->toPng();
                    break;
                case 'webp':
                    $encoded = $image->toWebp(77); // WebP con calidad 75%
                    break;
                case 'gif':
                    $encoded = $image->toGif();
                    break;
                case 'bmp':
                    $encoded = $image->toBitmap();
                    break;
                case 'tiff':
                    $encoded = $image->toTiff();
                    break;
                default:
                    $encoded =$imageData;
            }
            $fileName = $nombre.'_'. $indexImage . '.'.$extension;
            $urlPath = 'images/'.$id_producto.'/'. $fileName;
            $path = Storage::disk('public')->put($urlPath, $encoded);
            $url = Storage::url($urlPath);
            return responseSuccessService($url);
        } catch (Exception $e) {
            return responseErrorService($e);
        }
    }

    public function saveThumblr($id_producto,$imageInput){
        try {
            $extension = $imageInput->extension;
            $imageData = $imageInput->image;

            // Inicializar ImageManager con GdDriver
            $manager = new ImageManager(new GdDriver());
            $image = $manager->read($imageData);

            // Redimensionar la imagen manteniendo proporción
            $image = $image->scale(width: 300);

            switch ($extension) {
                case 'jpeg':
                case 'jpg':
                    $encoded = $image->toJpeg(70); // Reducimos calidad a 70%
                    break;
                case 'png':
                    $encoded = $image->toPng();
                    break;
                case 'webp':
                    $encoded = $image->toWebp(77); // WebP con calidad 75%
                    break;
                case 'gif':
                    $encoded = $image->toGif();
                    break;
                case 'bmp':
                    $encoded = $image->toBitmap();
                    break;
                case 'tiff':
                    $encoded = $image->toTiff();
                    break;
                default:
                    $encoded =$imageData;
            }
            $fileName = $id_producto . '.'.$extension;
            $urlPath = 'thumblr/'.$id_producto.'/'. $fileName;
            $path = Storage::disk('public')->put($urlPath, $encoded);
            $url = Storage::url($urlPath);
            return responseSuccessService($url);
        } catch (Exception $e) {
            return responseErrorService($e);
        }
    }
}