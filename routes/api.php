<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TarjetaController;

Route::get('/tarjetas', [TarjetaController::class, 'index']);

Route::get('/tarjetas/{id}', function () {
    return 'Buscando al tarjeta';
});

Route::post('/tarjetas', function () {
    return 'Creando tarjeta';
});

Route::put('/tarjetas/{id}', function () {
    return 'Actualizando tarjeta';
});

Route::delete('/tarjetas/{id}', function () {
    return 'Eliminando tarjeta';
});
