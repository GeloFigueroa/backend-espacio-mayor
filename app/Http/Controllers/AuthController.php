<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Intenta autenticar al usuario
        if (!Auth::attempt($request->only('email', 'password'))) {
            // Si falla, lanza un error de validación
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        // Si la autenticación es exitosa...
        $user = User::where('email', $request->email)->firstOrFail();

        // Genera un nuevo token para el usuario
        $token = $user->createToken('auth_token')->plainTextToken;

        // Devuelve el token y los datos del usuario como respuesta
        return response()->json([
            'message' => 'Login exitoso',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    public function register(Request $request)
    {
        // 1. Validar los datos de entrada
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed', // 'confirmed' busca un campo 'password_confirmation'
        ]);

        // 2. Crear el usuario en la base de datos
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            // ¡MUY IMPORTANTE! NUNCA guardes la contraseña en texto plano.
            // Hash::make() la encripta de forma segura.
            'password' => Hash::make($validatedData['password']),
        ]);

        // 3. (Opcional) Iniciar sesión inmediatamente creando un token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Usuario registrado exitosamente.',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ], 201); // 201: Created
    }

    public function logout(Request $request)
    {
        // Revoca el token actual que se usó para autenticar la petición
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada correctamente.'
        ]);
    }
}
