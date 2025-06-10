<?php

namespace App\Http\Controllers;

use App\Models\Tarjeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Lista;
use App\Http\Requests\StoreTarjetaRequest;
use Illuminate\Support\Facades\DB;

class TarjetaController extends Controller
{
    public function index()
    {
        // $tarjetas = Tarjeta::all();
        // return response()->json($tarjetas);

        $tarjetas = Tarjeta::orderBy('position', 'asc')->get();
        return response()->json($tarjetas);
    }

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
            'tipo_contenido'    => $validatedData['tipo_contenido'] ?? null,
            'contenido'         => $validatedData['contenido'] ?? [],
        ];

        if ($validatedData['tipo_contenido'] === Tarjeta::LISTADO_TARJETAS) {
            $tituloNuevaLista = $validatedData['titulo'] ?? 'Sin titulo';
            $tipoContenidoNuevaLista = $validatedData['contenido']['tipo_lista'] ?? Tarjeta::TIPO_BASICA;


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

    public function update(StoreTarjetaRequest $request, $id)
    {
        $tarjeta = Tarjeta::find($id);

        if (!$tarjeta) {
            return response()->json(['message' => 'Tarjeta no encontrada.', 'status' => 404], 404);
        }

        $validatedData = $request->validated();

        try {
            $updatedTarjeta = DB::transaction(function () use ($tarjeta, $validatedData) {
                $dataParaActualizar = [];
                if (isset($validatedData['titulo'])) {
                    $dataParaActualizar['titulo'] = $validatedData['titulo'];
                }
                if (isset($validatedData['subtitulo'])) {
                    $dataParaActualizar['subtitulo'] = $validatedData['subtitulo'];
                }
                if (isset($validatedData['color'])) {
                    $dataParaActualizar['color'] = $validatedData['color'];
                }
                if (isset($validatedData['imagenURL'])) {
                    $dataParaActualizar['imagenURL'] = $validatedData['imagenURL'];
                }
                if (isset($validatedData['firma'])) {
                    $dataParaActualizar['firma'] = $validatedData['firma'];
                }
                if (isset($validatedData['georeferenciacion'])) {
                    $dataParaActualizar['georeferenciacion'] = $validatedData['georeferenciacion'];
                }
                if (isset($validatedData['fecha_expiracion'])) {
                    $dataParaActualizar['fecha_expiracion'] = $validatedData['fecha_expiracion'];
                }
                if (isset($validatedData['diseno_tarjeta'])) {
                    $dataParaActualizar['diseno_tarjeta'] = $validatedData['diseno_tarjeta'];
                }
                if (isset($validatedData['nuevoTicket'])) {
                    $dataParaActualizar['nuevoTicket'] = $validatedData['nuevoTicket'];
                }
                if (array_key_exists('id_padre', $validatedData)) {
                    $dataParaActualizar['id_padre'] = $validatedData['id_padre'];
                }
                if (isset($validatedData['tipo_contenido'])) {
                    $dataParaActualizar['tipo_contenido'] = $validatedData['tipo_contenido'];
                }

                $currentContenido = $tarjeta->contenido ?? [];
                $newContenidoData = $validatedData['contenido'] ?? [];

                if (isset($newContenidoData['url'])) {
                    $currentContenido['url'] = $newContenidoData['url'];
                }

                if (isset($validatedData['tipo_contenido']) && $validatedData['tipo_contenido'] !== Tarjeta::LISTADO_TARJETAS) {
                    $currentContenido['id_lista'] = null;
                    $currentContenido['tipo_lista'] = null;
                } elseif ($tarjeta->tipo_contenido !== Tarjeta::LISTADO_TARJETAS && (!isset($validatedData['tipo_contenido']) || $validatedData['tipo_contenido'] !== Tarjeta::LISTADO_TARJETAS)) {
                    $currentContenido['id_lista'] = null;
                    $currentContenido['tipo_lista'] = null;
                }


                $tipoContenidoFinal = $validatedData['tipo_contenido'] ?? $tarjeta->tipo_contenido;
                if ($tipoContenidoFinal === Tarjeta::LISTADO_TARJETAS) {
                    $idListaExistente = $tarjeta->contenido['id_lista'] ?? null;
                    $lista = null;

                    if ($idListaExistente) {
                        $lista = Lista::find($idListaExistente);
                    }

                    $tituloParaLista = $validatedData['titulo'] ?? $tarjeta->titulo ?? 'Sin titulo';
                    $tipoParaLista = $validatedData['contenido']['tipo_lista'] ?? ($lista->tipoLista ?? Tarjeta::TIPO_BASICA);


                    if ($lista) {
                        $lista->update([
                            'tituloTarjeta' => $tituloParaLista,
                            'tipoLista'     => $tipoParaLista,
                        ]);
                    } else {
                        $lista = Lista::create([
                            'tituloTarjeta' => $tituloParaLista,
                            'tipoLista'     => $tipoParaLista,
                        ]);
                    }
                    $currentContenido['id_lista'] = $lista->id;
                    $currentContenido['tipo_lista'] = $lista->tipoLista;
                }

                $dataParaActualizar['contenido'] = $currentContenido;

                $tarjeta->update($dataParaActualizar);
                return $tarjeta;
            });

            return response()->json([
                'message' => 'Tarjeta actualizada correctamente.',
                'data'    => $updatedTarjeta->load('lista'),
                'status'  => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar la tarjeta.',
                'error'   => $e->getMessage(),
                'status'  => 500
            ], 500);
        }
    }

    /**
     * Elimina una tarjeta específica.
     *
     * @param  int  $id El ID de la Tarjeta a eliminar
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $tarjeta = Tarjeta::find($id);

            if (!$tarjeta) {
                return response()->json(['message' => 'Tarjeta no encontrada.', 'status' => 404], 404);
            }

            if ($tarjeta->tipo_contenido === Tarjeta::LISTADO_TARJETAS) {
                $idLista = $tarjeta->contenido['id_lista'] ?? null;
                if ($idLista) {
                    $lista = Lista::find($idLista);
                    if ($lista) {
                        $lista->delete();
                    }
                }
            }
            $tarjeta->delete();

            return response()->json(['message' => 'Tarjeta eliminada correctamente.', 'status' => 200], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar la tarjeta.',
                'error'   => $e->getMessage(),
                'status'  => 500
            ], 500);
        }
    }

    public function tarjetasListados()
    {
        try {
            $tarjetasDeListado = Tarjeta::where('tipo_contenido', Tarjeta::LISTADO_TARJETAS)
                ->orderBy('position', 'asc')
                ->get();

            if ($tarjetasDeListado->isEmpty()) {
                return response()->json([
                    'message' => 'No se encontraron tarjetas con tipo de contenido "listadoTarjetas".',
                    'data'    => [],
                    'status'  => 200
                ], 200);
            }

            return response()->json([
                'message' => 'Tarjetas con listas obtenidas correctamente.',
                'data'    => $tarjetasDeListado,
                'status'  => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las tarjetas de listado.',
                'error'   => $e->getMessage(),
                'status'  => 500
            ], 500);
        }
    }

    /**
     * Asegura que la tarjeta "Inicio" exista en la base de datos.
     * Si no existe, la crea con tipo_contenido "Listado" y una Lista asociada.
     *
     * @return Tarjeta La instancia de la tarjeta "Inicio".
     */
    protected function ensureInicioCardExists(): Tarjeta
    {
        return DB::transaction(function () {
            $tarjetaInicio = Tarjeta::where('titulo', 'Inicio')->first();

            if (!$tarjetaInicio) {
                Log::info("Tarjeta 'Inicio' no encontrada. Creándola ahora.");

                $nuevaListaParaInicio = Lista::create([
                    'tituloTarjeta' => 'Contenido de Inicio',
                    'tipoLista'     => Tarjeta::TIPO_BASICA,
                ]);
                $tarjetaInicio = Tarjeta::create([
                    'titulo'            => 'Inicio',
                    'subtitulo'         => 'Bienvenido/a a la sección principal.',
                    'color'             => '#FFFFFF',
                    'imagenURL'         => null,
                    'firma'             => null,
                    'georeferenciacion' => null,
                    'fecha_expiracion'  => null,
                    'diseno_tarjeta'    => Tarjeta::TIPO_BASICA,
                    'nuevoTicket'       => false,
                    'id_padre'          => null,
                    'tipo_contenido'    => Tarjeta::LISTADO_TARJETAS,
                    'contenido'         => [
                        'id_lista'   => $nuevaListaParaInicio->id,
                        'tipo_lista' => $nuevaListaParaInicio->tipoLista,
                        'url'        => null,
                    ],
                ]);
                Log::info("Tarjeta 'Inicio' creada con ID: " . $tarjetaInicio->id . " y Lista asociada ID: " . $nuevaListaParaInicio->id);
            } else {
                if (
                    $tarjetaInicio->tipo_contenido !== Tarjeta::LISTADO_TARJETAS ||
                    !isset($tarjetaInicio->contenido['id_lista']) ||
                    Lista::find($tarjetaInicio->contenido['id_lista']) === null
                ) {

                    Log::warning("Tarjeta 'Inicio' existe (ID: {$tarjetaInicio->id}) pero su configuración de lista es incorrecta o la lista no existe. Recreando lista asociada.");

                    $listaExistenteId = $tarjetaInicio->contenido['id_lista'] ?? null;
                    if ($listaExistenteId) {
                        $lista = Lista::find($listaExistenteId);
                        if (!$lista) {
                            $listaExistenteId = null;
                        }
                    }

                    $listaParaInicio = Lista::firstOrCreate(
                        ['id' => $listaExistenteId],
                        [
                            'tituloTarjeta' => $tarjetaInicio->titulo ?? 'Contenido de Inicio',
                            'tipoLista'     => Tarjeta::TIPO_BASICA,
                        ]
                    );

                    $contenidoActualizado = $tarjetaInicio->contenido;
                    $contenidoActualizado['id_lista'] = $listaParaInicio->id;
                    $contenidoActualizado['tipo_lista'] = $listaParaInicio->tipoLista;

                    $tarjetaInicio->update([
                        'tipo_contenido' => Tarjeta::LISTADO_TARJETAS,
                        'contenido' => $contenidoActualizado
                    ]);
                    Log::info("Configuración de lista para Tarjeta 'Inicio' (ID: {$tarjetaInicio->id}) corregida. Lista asociada ID: " . $listaParaInicio->id);
                }
            }
            return $tarjetaInicio;
        });
    }

    /**
     * Obtiene la tarjeta con el título 'Inicio', asegurando su existencia.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTarjetaInicio()
    {
        try {
            $tarjetaInicio = $this->ensureInicioCardExists();

            return response()->json([
                'message' => "Tarjeta 'Inicio' obtenida/asegurada correctamente.",
                'data'    => $tarjetaInicio,
                'status'  => 200
            ], 200);
        } catch (\Exception $e) {
            Log::error("Error crítico al obtener/asegurar la tarjeta 'Inicio': " . $e->getMessage());
            return response()->json([
                'message' => "Error crítico al obtener/asegurar la tarjeta 'Inicio'.",
                'error'   => $e->getMessage(),
                'status'  => 500
            ], 500);
        }
    }

    public function obtenerTarjetasSinPadre()
    {
        try {
            $tarjetasSinPadre = Tarjeta::whereNull('id_padre')->get();

            return response()->json([
                'message' => 'Tarjetas sin padre obtenidas correctamente.',
                'data' => $tarjetasSinPadre,
                'status' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al obtener las tarjetas sin padre.',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }

    public function updateOrder(Request $request)
    {
        $validated = $request->validate([
            'id_padre' => 'required|integer|exists:tarjetas,id',
            'ordered_ids' => 'required|array',
            'ordered_ids.*' => 'integer|exists:tarjetas,id',
        ]);

        DB::transaction(function () use ($validated) {
            $padreId = $validated['id_padre'];

            foreach ($validated['ordered_ids'] as $index => $tarjetaId) {
                Tarjeta::where('id', $tarjetaId)
                    ->where('id_padre', $padreId)
                    ->update(['position' => $index]);
            }
        });

        return response()->json([
            'message' => 'El orden de las tarjetas ha sido actualizado correctamente.',
            'status' => 200,
        ]);
    }
}
