<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function register(Request $request){
        try {
            $data = $request->validate([
                'nombre' => ['required', 'string'],
                'celular' => ['required', 'string', 'unique:users'],
                'email' => ['required', 'email', 'unique:users'],
                'password' => ['required', 'min:6'],
                'confirmPassword' => ['required'],
            ], 
            [
                'celular.unique' => 'El número de celular ya está registrado.',
                'email.unique' => 'El correo electrónico ya está en uso.',
                'email.email' => 'Ingrese un correo válido.',
                'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            ]);
    
            if ($data['password'] !== $data['confirmPassword']) {
                throw new Exception("Las contraseñas no coinciden");
            }
    
            // Crear usuario con contraseña hasheada
            $user = User::create([
                "nombre" => strtoupper($data["nombre"]),
                "celular" => $data["celular"],
                "password" => Hash::make($data["password"]), // Hashear la contraseña
                "id_pais" => 1,
                "email" => $data["email"],
            ]);

            // Iniciar sesión automáticamente después del registro
            $credentials = [
                "email"=>$user["email"],
                "password"=>$data["password"]
            ];
            // Intentar autenticar al usuario y guardar en la sesión

            if (Auth::attempt($credentials)) {
                $user_log = Auth::user();
                if ($user_log instanceof User) {
                    $user_log->makeHidden(['id', 'created_at', 'updated_at']);  // Ocultar campos
                    // Encriptar la data antes de enviarla
                    return response()->json([
                        "ok" => true,
                        'data' => $this->encriptarData($user_log)
                    ]);
                } else {
                    return response()->json([
                        "ok" => false,
                        "message" => "Usuario no encontrado"
                    ],401);
                }
            }

            return response()->json([
                "ok" => false,
                'message' => 'Credenciales incorrectas'
            ], 401);
    
        } catch (Exception $e) {
            Log::error($e);
            return response([
                "ok" => false,
                "message" => $e->getMessage()
            ], 401);
        }
    }

    public function login(Request $request){
        try {
            $data = $request->validate([
                'username'=>['required','string'],
                'password'=>['required','min:6'],
            ]);
    
            // Verifica si el username es un correo o un número
            if (strpos($data['username'], '@')) {
                $user = User::where('email', $data['username'])->first();
                $credentials = ['email' => $data['username'], 'password' => $data['password']];
            } else {
                $number = ltrim($data['username'], '0');
                $user = User::where('celular', $number)->first();
                $credentials = ['celular' => $number, 'password' => $data['password']];
            }
    
            if (!$user) {
                return response()->json([
                    "ok" => false,
                    'message' => 'El usuario no existe'
                ], 401);
            }
    
            // Intentar autenticar al usuario y guardar en la sesión
            if (Auth::attempt($credentials)) {
                $user_log = Auth::user();
                if ($user_log instanceof User) {
                    $user_log->makeHidden(['id', 'created_at', 'updated_at']);  // Ocultar campos
                    // Encriptar la data antes de enviarla
                    return response()->json([
                        "ok" => true,
                        'data' => $this->encriptarData($user_log)
                    ]);
                } else {
                    return response()->json([
                        "ok" => false,
                        "message" => "Usuario no encontrado"
                    ],401);
                }
            }
    
            return response()->json([
                "ok" => false,
                'message' => 'Credenciales incorrectas'
            ], 401);
    
        } catch (Exception $e) {
            Log::error($e);
            return response()->json([
                "ok" => false,
                "message" => $e->getMessage()
            ], 400);
        }
    }


    public function cerrarSession(Request $request)
    {
        try {   
            $request->user()->tokens()->delete();
            // Invalidar la sesión (IMPORTANTE para HttpOnly cookies)
            $request->session()->invalidate();
            $request->session()->regenerateToken();


            return response()->json(["ok"=>true],200)
                ->withCookie(cookie()->forget('XSRF-TOKEN')) // Eliminar token CSRF
                ->withCookie(cookie()->forget('laravel_session')); // Eliminar sesión de Laravel
        } catch (Exception $e) {
            Log::error($e);
            return response()->json([
                "ok" => false,
                "message" => $e->getMessage()
            ], 400);
        }
    }

    public function getUser(Request $request){
        try {
            $user = Auth::user();
            if(!$user)throw new  Exception("No existe un usuario autenticado");
            if ($user instanceof User) {
                $user->makeHidden(['id', 'created_at', 'updated_at']);  // Ocultar campos
                // Encriptar la data antes de enviarla
                return response()->json([
                    "ok" => true,
                    'data' => $this->encriptarData($user)
                ]);
            } else {
                return response()->json([
                    "ok" => false,
                    "message" => "Usuario no encontrado"
                ],401);
            }
        }catch (Exception $e) {
            Log::error($e);
            return response([
                "ok"=>false,
                "message"=>$e->getMessage()
            ],401);                 
        }
    }

    public function encriptarData($data_json){
        $secretKey = env("SECRET_KEY_DATA"); // Debe ser de 32 caracteres
        $cipher = 'AES-256-CBC';
    
        $data = json_encode($data_json); // Convertimos el usuario en JSON
    
        $key = hash('sha256', $secretKey, true); // Convertir clave a SHA-256 (32 bytes)
        $iv = openssl_random_pseudo_bytes(16); // Generar IV de 16 bytes
    
        $encryptedData = openssl_encrypt($data, $cipher, $key, OPENSSL_RAW_DATA, $iv);
        
        return base64_encode($iv . $encryptedData); // Concatenar IV + Datos encriptados y codificar en Base64
    
    }
}
