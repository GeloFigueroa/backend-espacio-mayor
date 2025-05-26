<?php

namespace App\Http\Controllers;

use App\Models\Lista;
use Illuminate\Http\Request;

class ListaController extends Controller
{
    //pedir una lista por id
    public function show(string $id)
    {
        $lista = Lista::with('tarjetas')->findOrFail($id);
        return response()->json($lista);
    }
    
    // Crear una nueva lista
    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255'
        ]);
        
        $lista = Lista::create($request->only('titulo'));
        
        return response()->json($lista, 201);
    }


    // Agregar tarjeta a una lista
    public function addTarjeta(Request $request, $listaId)
    {
        $request->validate([
            'tarjeta_id' => 'required|exists:tarjetas,id'
        ]);
        
        $lista = Lista::findOrFail($listaId);
        $lista->tarjetas()->attach($request->tarjeta_id);
        
        return response()->json(['message' => 'Tarjeta agregada a la lista']);
    }
}
