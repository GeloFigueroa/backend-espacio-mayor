<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tarjeta extends Model
{
    protected $table = 'tarjetas';

    protected $fillable = [
        'titulo',
        'subtitulo',
        'categoria',
        'color',
        'imagenURL',
        'firma',
        'georeferenciacion',
        'tiempoExpiracion',
        'tipo',
        'favorito',
        'nuevoTicket',
        'path' 
    ];
}
