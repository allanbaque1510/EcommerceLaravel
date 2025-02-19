<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarritoItems extends Model
{
    use HasFactory;
    protected $table = "carrito_items";
    protected $fillable = [
        "id_carrito",
        "id_producto",
        "cantidad",
    ];
}
