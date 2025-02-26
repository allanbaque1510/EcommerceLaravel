<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\InventarioController;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/session', [AuthController::class,'createAnonymousSession']);
Route::post('/register', [AuthController::class,'register']);
Route::post('/login', [AuthController::class,'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/v',[AuthController::class,'verificarToken']);
    Route::get('/user', [AuthController::class, 'getUser']);
    Route::get('/logout', [AuthController::class,'cerrarSession']);
    Route::post('/upload_product', [InventarioController::class,'upload_product']);
    
});

Route::get('/get_products',[IndexController::class,'get_products']);
Route::post('/save_product',[CarritoController::class,'saveProductsCarrito']);

