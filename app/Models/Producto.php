<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'productos';
    use HasFactory;

    protected $fillable= [
        "id",
        "nombre",	
        "descripcion",	
        "precio",	
        "stock",	
        "estado",	
        "user_id",
    ];

}
