<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lista extends Model
{
    use HasFactory;

    protected $fillable = [
        'tituloTarjeta',
        'tipoLista'
    ];

    public function tarjetas()
{
    return $this->hasMany(Tarjeta::class, 'id_padre', 'id');
}

}
