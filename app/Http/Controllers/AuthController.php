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
}