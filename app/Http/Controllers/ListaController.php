<?php

namespace App\Http\Controllers;

use App\Models\Lista;
use Illuminate\Http\Request;

class ListaController extends Controller
{
    // Obtener todas las listas
    public function index()
    {
        $listas = Lista::with('tarjetas')->get();
        return response()->json($listas);
    }

    // Mostrar una lista específica
    public function show($id)
    {
        $lista = Lista::with('tarjetas')->find($id);

        if (!$lista) {
            return response()->json(['message' => 'Lista no encontrada'], 404);
        }

        return response()->json($lista);
    }

    // Crear una nueva lista
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tituloTarjeta' => 'required|string|max:255',
            'tipoLista' => 'required|string|max:255',
        ]);

        $lista = Lista::create($validated);

        return response()->json([
            'message' => 'Lista creada correctamente',
            'data' => $lista,
        ], 201);
    }

    // Actualizar una lista existente
    public function update(Request $request, $id)
    {
        $lista = Lista::find($id);

        if (!$lista) {
            return response()->json(['message' => 'Lista no encontrada'], 404);
        }

        $validated = $request->validate([
            'tituloTarjeta' => 'sometimes|required|string|max:255',
            'tipoLista' => 'sometimes|required|string|max:255',
        ]);

        $lista->update($validated);

        return response()->json([
            'message' => 'Lista actualizada correctamente',
            'data' => $lista,
        ]);
    }

    // Eliminar una lista
    public function destroy($id)
    {
        $lista = Lista::find($id);

        if (!$lista) {
            return response()->json(['message' => 'Lista no encontrada'], 404);
        }

        $lista->delete();

        return response()->json(['message' => 'Lista eliminada correctamente']);
    }
}
