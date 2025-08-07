<?php

namespace App\Http\Controllers;

use App\Models\Tarjeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Lista;
use App\Http\Requests\StoreTarjetaRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class TarjetaController extends Controller
{
    public function index()
    {
        $tarjetas = Tarjeta::orderBy('position', 'asc')->get();
        return response()->json(['data' => $tarjetas]);
    }

    public function store(StoreTarjetaRequest $request)
    {
        $validatedData = $request->validated();

        try {
            $tarjeta = DB::transaction(function () use ($validatedData) {

                $tipoContenido = $validatedData['tipo_contenido'] ?? null;

                if (in_array($tipoContenido, Tarjeta::$listBasedContentTypes)) {

                    $lista = Lista::create([
                        'tituloTarjeta' => $validatedData['titulo'] ?? 'Sin titulo',
                        'tipoLista'     => $validatedData['contenido']['tipo_lista'] ?? Tarjeta::TIPO_BASICA,
                    ]);

                    $validatedData['contenido']['id_lista'] = $lista->id;
                    $validatedData['contenido']['tipo_lista'] = $lista->tipoLista;
                }
                return Tarjeta::create($validatedData);
            });

            return response()->json([
                'message' => 'Tarjeta creada correctamente',
                'data'    => $tarjeta->load('lista'),
                'status'  => 201
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error al crear la tarjeta', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return response()->json([
                'message' => 'Error al crear la tarjeta.',
                'error'   => $e->getMessage(),
                'status'  => 500
            ], 500);
        }
    }

    protected function ensureDefaultCardExists(string $titulo, ?string $subtitulo, string $tipoContenido, array $contenido = [], string $diseno = Tarjeta::TIPO_BASICA): Tarjeta
    {
        return DB::transaction(function () use ($titulo, $subtitulo, $tipoContenido, $contenido, $diseno) {

            $tarjeta = Tarjeta::firstOrCreate(
                ['titulo' => $titulo],
                [
                    'subtitulo'         => $subtitulo,
                    'tipo_contenido'    => $tipoContenido,
                    'diseno_tarjeta'    => $diseno,
                    'contenido'         => $contenido,
                    'nuevoTicket'       => false,
                    'contenido_puntos'  => [],
                    'boton_accion'      => false,
                ]
            );

            if ($tarjeta->wasRecentlyCreated && $tipoContenido === Tarjeta::LISTADO_TARJETAS) {

                Log::info("Tarjeta '$titulo' no encontrada. Creando su Lista asociada.");

                $nuevaLista = Lista::create([
                    'tituloTarjeta' => $titulo,
                    'tipoLista'     => $diseno,
                ]);

                $contenidoActualizado = $tarjeta->contenido;
                $contenidoActualizado['id_lista'] = $nuevaLista->id;
                $contenidoActualizado['tipo_lista'] = $nuevaLista->tipoLista;

                $tarjeta->contenido = $contenidoActualizado;
                $tarjeta->save();

                Log::info("Tarjeta '$titulo' creada con ID: {$tarjeta->id} y Lista asociada ID: {$nuevaLista->id}");
            }

            return $tarjeta;
        });
    }


    public function update(StoreTarjetaRequest $request, $id)
    {
        $tarjeta = Tarjeta::findOrFail($id);
        $validatedData = $request->validated();

        try {
            $updatedTarjeta = DB::transaction(function () use ($tarjeta, $validatedData) {
                $dataParaActualizar = $validatedData;

                $currentContenido = $tarjeta->contenido ?? [];
                if (isset($validatedData['contenido'])) {
                    $currentContenido = array_merge($currentContenido, $validatedData['contenido']);
                }

                $tipoContenidoFinal = $validatedData['tipo_contenido'] ?? $tarjeta->tipo_contenido;

                if (in_array($tipoContenidoFinal, Tarjeta::$listBasedContentTypes)) {

                    $idListaExistente = $tarjeta->contenido['id_lista'] ?? null;
                    $lista = $idListaExistente ? Lista::find($idListaExistente) : null;

                    $datosLista = [
                        'tituloTarjeta' => $validatedData['titulo'] ?? $tarjeta->titulo,
                        'tipoLista'     => $validatedData['contenido']['tipo_lista'] ?? ($lista->tipoLista ?? Tarjeta::TIPO_BASICA)
                    ];

                    if ($lista) {
                        $lista->update($datosLista);
                    } else {
                        $lista = Lista::create($datosLista);
                    }

                    $currentContenido['id_lista'] = $lista->id;
                    $currentContenido['tipo_lista'] = $lista->tipoLista;
                } else {
                    $currentContenido['id_lista'] = null;
                    $currentContenido['tipo_lista'] = null;
                }

                $dataParaActualizar['contenido'] = $currentContenido;

                $tarjeta->update($dataParaActualizar);
                return $tarjeta;
            });

            return response()->json([
                'message' => 'Tarjeta actualizada correctamente.',
                'data'    => $updatedTarjeta->load('lista'),
                'status'  => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar la tarjeta.',
                'error'   => $e->getMessage(),
                'status'  => 500
            ]);
        }
    }

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

    // Detectores automaticos de tarjetas en la base.
    protected function ensureInicioCardExists(): Tarjeta
    {
        return $this->ensureDefaultCardExists(
            'Inicio',
            'Bienvenido/a a la sección principal.',
            Tarjeta::LISTADO_TARJETAS
        );
    }

    protected function ensureAyudaCardExists(): Tarjeta
    {
        return $this->ensureDefaultCardExists(
            'Ayuda',
            'Encuentra soporte y preguntas frecuentes.',
            Tarjeta::INFO_AYUDA_CONTENIDO
        );
    }

    protected function ensurePagoServiciosCardExists(): Tarjeta
    {
        return $this->ensureDefaultCardExists(
            'Pago de servicios',
            'Paga tus cuentas de forma fácil y segura.',
            Tarjeta::LISTADO_TARJETAS
        );
    }

    protected function ensureMediosDeTransporteCardExists(): Tarjeta
    {
        return $this->ensureDefaultCardExists(
            'Pago de servicios',
            'Paga tus cuentas de forma fácil y segura.',
            Tarjeta::LISTADO_TARJETAS
        );
    }

    public function getTarjetaPagoServicios()
    {
        try {
            $tarjeta = $this->ensurePagoServiciosCardExists();

            return response()->json([
                'message' => "Tarjeta 'Pago de servicios' obtenida/asegurada correctamente.",
                'data'    => $tarjeta,
                'status'  => 200
            ], 200);
        } catch (\Exception $e) {
            Log::error("Error crítico al obtener/asegurar la tarjeta 'Pago de servicios': " . $e->getMessage());
            return response()->json([
                'message' => "Error crítico al obtener/asegurar la tarjeta 'Pago de servicios'.",
                'error'   => $e->getMessage(),
                'status'  => 500
            ], 500);
        }
    }


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

    public function getTarjetaAyuda()
    {
        try {
            $tarjetaAyuda = $this->ensureAyudaCardExists();

            return response()->json([
                'message' => "Tarjeta 'Ayuda' obtenida/asegurada correctamente.",
                'data'    => $tarjetaAyuda,
                'status'  => 200
            ], 200);
        } catch (\Exception $e) {
            Log::error("Error crítico al obtener/asegurar la tarjeta 'Ayuda': " . $e->getMessage());
            return response()->json([
                'message' => "Error crítico al obtener/asegurar la tarjeta 'Ayuda'.",
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
            'id_padre' => 'required|integer|exists:listas,id',
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

    public function checkUpdates(Request $request)
    {
        $request->validate([
            'last_updated_at' => 'required|date',
        ]);

        $clientLastUpdate = Carbon::parse($request->input('last_updated_at'));

        // Si la fecha es del futuro, forzar retorno false
        if ($clientLastUpdate->greaterThan(Carbon::now())) {
            return response()->json([
                'needs_update' => false,
                'server_timestamp' => Carbon::now()->toIso8601String(),
            ]);
        }

        $serverLastUpdate = Carbon::parse(Cache::get('last_tarjeta_update_timestamp', '2000-01-01 00:00:00'));

        $needsUpdate = $serverLastUpdate->isAfter($clientLastUpdate);

        return response()->json([
            'needs_update' => $needsUpdate,
            'server_timestamp' => $serverLastUpdate->toIso8601String(),
        ]);
    }
}
