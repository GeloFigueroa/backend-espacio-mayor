<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CuponController extends Controller
{
    // GET /api/cupones/disponible
    public function disponible(Request $request)
    {
        // Opcional: por usuario (ej: límite 1 por día)
        // $usuarioId = $request->user()?->id ?? $request->input('usuario_id');

        $cupon = Cupon::where('usado', false)->first();

        if (!$cupon) {
            return response()->json(['message' => 'No hay cupones disponibles'], 404);
        }

        return response()->json([
            'id'          => $cupon->id,
            'codigo'      => $cupon->codigo,
            'descripcion' => $cupon->descripcion,
            'lote'        => $cupon->lote,
        ]);
    }

    // POST /api/cupones/usar { codigo, usuario_id? }
    public function usar(Request $request)
{
    $data = $request->validate([
        'codigo' => 'required|string',
        'rut'    => 'required|string',
    ]);

    $cupon = Cupon::where('codigo', $data['codigo'])->first();

    if (!$cupon) {
        return response()->json(['message' => 'Código no válido'], 404);
    }

    if ($cupon->usado) {
        return response()->json(['message' => 'Este cupón ya fue usado'], 409);
    }

    $cupon->update([
        'usado' => true,
        'rut' => $data['rut'],
        'fecha_uso' => now(),
    ]);

    return response()->json(['message' => 'Cupón marcado como usado']);
}


    // SOLO PARA QA (deshabilitar en prod)
    public function reset()
    {
        Cupon::where('usado', true)->update(['usado' => false, 'usuario_id' => null, 'fecha_uso' => null]);
        return response()->json(['message' => 'Reseteado OK']);
    }
}
