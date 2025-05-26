<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ListaController;
use App\Http\Controllers\TarjetaController;

//Tarjetas
Route::get('/tarjetas', [TarjetaController::class, 'index']);
Route::post('/tarjetas', [TarjetaController::class, 'store']);
Route::delete('/tarjetas/{id}', [TarjetaController::class, 'delete']);
Route::put('/tarjetas/{id}', [TarjetaController::class, 'update']);

//Listas
Route::get('listas/{id}', [ListaController::class, 'show']);
Route::post('listas', [ListaController::class, 'store']);
// Route::post('listas/{listaId}/tarjetas', [ListaController::class, 'addTarjeta']);