<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CarritoController extends Controller
{
    public function saveProductsInSession(Request $request){
        try {
            
            // Auth::check();
            Log::info($request);
        } catch (Exception $e) {
            Log::error($e);
            return response([
                "ok" => false,
                "message" => $e->getMessage()
            ], 400);
        }
    }
}
