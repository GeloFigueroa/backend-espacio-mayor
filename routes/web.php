<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TarjetaController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('tarjetas', [TarjetaController::class, 'index']);
