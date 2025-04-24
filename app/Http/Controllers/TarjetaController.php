<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tarjeta;
use Illuminate\Support\Facades\Validator;

class TarjetaController extends Controller
{
    public function index()
    {
        $tarjetas = Tarjeta::all();
        return response()->json($tarjetas);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'titulo' => 'required',
            'subtitulo' => 'required',
            'categoria' => 'required',
            'color' => 'required',
            'firma' => 'required',
            'imagenURL' => 'required',
            'georeferenciacion' => 'required',
            'tiempoExpiracion' => 'required',
            'tipo' => 'required',
            'favorito' => 'required',
            'nuevoTicket' => 'required',
            'path' => 'required',
        ]);

        if($validator->fails()){
            $data = [
                'message' => 'Error en la validacion de los datos',
                'error' => $validator->errors(),
                'status' => 400
            ];
            return response()->json($data, 400);
        }

        $tarjeta = Tarjeta::create($request->only([
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
        ]));

        if(!$tarjeta) {
            $data = [
                'message' => 'Error al crear la tarjeta',
                'status' => 500
            ];
            return response()->json($data, 500);
        }
    
        return response()->json([
            'message' => 'Tarjeta creada correctamente',
            'data' => $tarjeta,
            'status' => 201
        ], 201);


    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
