<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tarjeta;
use App\Models\Lista;
use App\Http\Requests\StoreTarjetaRequest;

class TarjetaController extends Controller
{
    public function index()
    {
        $tarjetas = Tarjeta::all();
        return response()->json($tarjetas);
    }

    //creacion de nueva Tarjeta
    public function store(StoreTarjetaRequest $request)
    {
        $validatedData = $request->validated();

        $dataParaCrear = [
            'titulo'            => $validatedData['titulo'] ?? null,
            'subtitulo'         => $validatedData['subtitulo'] ?? null,
            'color'             => $validatedData['color'] ?? null,
            'imagenURL'         => $validatedData['imagenURL'] ?? null,
            'firma'             => $validatedData['firma'] ?? null,
            'georeferenciacion' => $validatedData['georeferenciacion'] ?? null,
            'fecha_expiracion'  => $validatedData['fecha_expiracion'] ?? null,
            'diseno_tarjeta'    => $validatedData['diseno_tarjeta'],
            'nuevoTicket'       => $validatedData['nuevoTicket'] ?? false,
            'id_padre'          => $validatedData['id_padre'] ?? null,
            'tipo_contenido'    => $validatedData['tipo_contenido'],
            'contenido'         => $validatedData['contenido'] ?? [],
        ];

        // Si tipo_contenido es 'listadoTarjetas', creamos una nueva lista
        if ($validatedData['tipo_contenido'] === Tarjeta::LISTADO_TARJETAS) {
            $tituloNuevaLista = $validatedData['titulo'] ?? 'Sin titulo';
            $tipoContenidoNuevaLista = $validatedData['contenido']['tipo_lista'];

            $nuevaLista = Lista::create([
                'tituloTarjeta' => $tituloNuevaLista,
                'tipoLista'     => $tipoContenidoNuevaLista,
            ]);

            $dataParaCrear['contenido'] = array_merge(
                $dataParaCrear['contenido'],
                [
                    'id_lista'   => $nuevaLista->id,
                    'tipo_lista' => $nuevaLista->tipoLista,
                ]
            );
        }

        $tarjeta = Tarjeta::create($dataParaCrear);

        if (!$tarjeta) {
            return response()->json(['message' => 'Error al crear la tarjeta', 'status' => 500], 500);
        }

        return response()->json([
            'message' => 'Tarjeta creada correctamente',
            'data'    => $tarjeta->load('lista'),
            'status'  => 201
        ], 201);
    }
}
