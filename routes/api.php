<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ListaController;
use App\Http\Controllers\TarjetaController;

//Tarjetas
Route::get('/tarjetas', [TarjetaController::class, 'index']);
Route::post('/tarjetas', [TarjetaController::class, 'store']);

//Listas
Route::apiResource('/listas', ListaController::class);