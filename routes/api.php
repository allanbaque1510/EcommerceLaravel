<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CarritoController;
use Illuminate\Support\Facades\Route;

Route::get('/session', [AuthController::class,'createAnonymousSession']);
Route::post('/register', [AuthController::class,'register']);
Route::post('/login', [AuthController::class,'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', [AuthController::class, 'getUser']);
    Route::get('/logout', [AuthController::class,'cerrarSession']);
    Route::get('/verifyToken', [AuthController::class,'verificarToken']);
});
Route::post('/save_product',[CarritoController::class,'saveProductsCarrito']);

