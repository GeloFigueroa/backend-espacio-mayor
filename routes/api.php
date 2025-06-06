<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ListaController;
use App\Http\Controllers\TarjetaController;

//Login
Route::post('/login', [AuthController::class, 'login']);

//Tarjetas
Route::get('/tarjetas', [TarjetaController::class, 'index']);
Route::post('/tarjetas', [TarjetaController::class, 'store']);
Route::put('/tarjetas/{id}', [TarjetaController::class, 'update']);
Route::get('/tarjetas/listados', [TarjetaController::class, 'tarjetasListados']); //peticiones de tarjetas contenido lista.
Route::delete('/tarjetas/{id}', [TarjetaController::class, 'destroy']); //eliminar mis tarjetas con el control de listas.
Route::get('/tarjetas/inicio', [TarjetaController::class, 'getTarjetaInicio']);
Route::get('/tarjetas/sin-padre', [TarjetaController::class, 'obtenerTarjetasSinPadre']);



//Listas
Route::apiResource('/listas', ListaController::class);