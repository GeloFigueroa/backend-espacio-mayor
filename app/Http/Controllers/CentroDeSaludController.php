<?php

namespace App\Http\Controllers;

use App\Models\CentroDeSalud;
use Illuminate\Http\Request;

class CentroDeSaludController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $centrosDeSalud = CentroDeSalud::all();

            return response()->json([
                'message' => 'Centros de salud obtenidos correctamente.',
                'data' => $centrosDeSalud
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error al obtener centros de salud: ' . $e->getMessage());

            return response()->json([
                'message' => 'Ocurrió un error al obtener los datos.',
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(CentroDeSalud $centroDeSalud)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CentroDeSalud $centroDeSalud)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CentroDeSalud $centroDeSalud)
    {
        //
    }
}
