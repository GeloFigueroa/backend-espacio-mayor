<?php

namespace App\Observers;

use App\Models\Tarjeta;
use App\Services\FirebaseNotificationService;
use Illuminate\Support\Facades\Log;

class TarjetaObserver
{
    private FirebaseNotificationService $firebaseService;

    public function __construct(FirebaseNotificationService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function created(Tarjeta $tarjeta): void
    {
        Log::info('Observer: Tarjeta creada con ID ' . $tarjeta->id);
        $this->sendNotification('tarjeta_creada', $tarjeta);
    }

    public function updated(Tarjeta $tarjeta): void
    {
        Log::info('Observer: Tarjeta actualizada con ID ' . $tarjeta->id);
        $this->sendNotification('tarjeta_actualizada', $tarjeta);
    }

    public function deleted(Tarjeta $tarjeta): void
    {
        Log::info('Observer: Tarjeta eliminada con ID ' . $tarjeta->id);
        // Para deleted, es mejor no enviar todo el objeto, solo su ID
        $this->firebaseService->sendToTopic('actualizaciones', [
            'accion' => 'tarjeta_eliminada',
            'id' => $tarjeta->id,
            'id_padre' => $tarjeta->id_padre, // Para saber qué lista refrescar
        ]);
    }

    /**
     * Helper para enviar la notificación.
     */
    private function sendNotification(string $accion, Tarjeta $tarjeta): void
    {
        // El 'payload' es el conjunto de datos que recibirá tu app Flutter
        $payload = [
            'accion' => $accion,
            'id' => $tarjeta->id,
            'titulo' => $tarjeta->titulo ?? '',
            'id_padre' => $tarjeta->id_padre ?? '',
            // Puedes añadir cualquier otro campo que necesites en la app
        ];

        // Usamos el servicio para enviar la notificación al topic 'actualizaciones'
        $this->firebaseService->sendToTopic('actualizaciones', $payload);
    }
}