<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ListaController;
use App\Http\Controllers\CentroDeSaludController;
use App\Http\Controllers\TarjetaController;

//Login
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

//consumo de la app
Route::get('/tarjetas/inicio', [TarjetaController::class, 'getTarjetaInicio']);
Route::get('/tarjetas/ayuda', [TarjetaController::class, 'getTarjetaAyuda']);

Route::apiResource('/listas', ListaController::class);
Route::get('/listas/ids/todas', [ListaController::class, 'getAllIds']);

Route::get('/centros-de-salud', [CentroDeSaludController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [AuthController::class, 'logout']);

    //Tarjetas
    Route::get('/tarjetas', [TarjetaController::class, 'index']);
    Route::get('/tarjetas/listados', [TarjetaController::class, 'tarjetasListados']);
    Route::get('/tarjetas/sin-padre', [TarjetaController::class, 'obtenerTarjetasSinPadre']);
    Route::post('/tarjetas', [TarjetaController::class, 'store']);
    Route::put('/tarjetas/{id}', [TarjetaController::class, 'update']);
    Route::delete('/tarjetas/{id}', [TarjetaController::class, 'destroy']);
    Route::post('/tarjetas/update-order', [TarjetaController::class, 'updateOrder']);
});
