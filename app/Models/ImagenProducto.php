<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImagenProducto extends Model
{
    protected $table = 'imagen_producto';
    use HasFactory;

    protected $fillable= [
        "id",
        "id_producto",	
        "url_image",	
        "color",	
        "estado",	
        "id_usuario",
    ];

}
