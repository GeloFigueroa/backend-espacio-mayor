<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cupon extends Model
{
    protected $table = 'cupones';

   protected $fillable = [
    'codigo', 'descripcion', 'lote', 'usado', 'rut', 'fecha_uso'
];


    protected $casts = [
        'usado' => 'boolean',
        'fecha_uso' => 'datetime',
    ];
}
