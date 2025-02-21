<?php

use Illuminate\Support\Facades\Log;

if (!function_exists('responseErrorService')) {
    function responseErrorService(Exception $error){
        Log::error($error);
        $response = new \stdClass();
        $response->ok = false;
        $response->message = $error->getMessage();
        return $response;
    }
}

if (!function_exists('responseErrorController')) {
    function responseErrorController(Exception $error, $codigo = 400) {
        Log::error($error);
        $response = new \stdClass();
        $response->ok = false;
        $response->message = $error->getMessage();
        
        return response()->json($response, $codigo);
    }
}


if (!function_exists('responseSuccessService')) {
    function responseSuccessService($data) {
        $response = new \stdClass();
        $response->ok = true;
        $response->data = $data;
        return $response;
    }
}

if(!function_exists('encriptarDataJson')){
    function encriptarDataJson($data_json){
        $secretKey = env("SECRET_KEY_DATA"); // Debe ser de 32 caracteres
        $cipher = 'AES-256-CBC';
        $data = json_encode($data_json); // Convertimos el usuario en JSON
        $key = hash('sha256', $secretKey, true); // Convertir clave a SHA-256 (32 bytes)
        $iv = openssl_random_pseudo_bytes(16); // Generar IV de 16 bytes
        $encryptedData = openssl_encrypt($data, $cipher, $key, OPENSSL_RAW_DATA, $iv);

        return base64_encode($iv . $encryptedData); // Concatenar IV + Datos encriptados y codificar en Base64
    }
}