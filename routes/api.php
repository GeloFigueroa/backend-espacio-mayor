<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ListaController;
use App\Http\Controllers\TarjetaController;

//Login
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {

    // Ruta para verificar el token y obtener datos del usuario logueado
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Ruta para cerrar sesión (revocar el token)
    Route::post('/logout', [AuthController::class, 'logout']);

    //Tarjetas
    Route::get('/tarjetas', [TarjetaController::class, 'index']);
    Route::post('/tarjetas', [TarjetaController::class, 'store']);
    Route::put('/tarjetas/{id}', [TarjetaController::class, 'update']);
    Route::get('/tarjetas/listados', [TarjetaController::class, 'tarjetasListados']); // Nombre corregido
    Route::delete('/tarjetas/{id}', [TarjetaController::class, 'destroy']);
    Route::get('/tarjetas/inicio', [TarjetaController::class, 'getTarjetaInicio']);
    Route::get('/tarjetas/sin-padre', [TarjetaController::class, 'obtenerTarjetasSinPadre']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/tarjetas/update-order', [TarjetaController::class, 'updateOrder']);
    });

    //Listas
    Route::apiResource('/listas', ListaController::class);
});
