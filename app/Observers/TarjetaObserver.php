<?php

namespace App\Observers;

use App\Models\Tarjeta;
use Illuminate\Support\Facades\Cache;
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
        $this->updateLastModifiedTimestamp();
        $this->sendNotification('tarjeta_creada', $tarjeta);
    }

    public function updated(Tarjeta $tarjeta): void
    {
        Log::info('Observer: Tarjeta actualizada con ID ' . $tarjeta->id);
        $this->updateLastModifiedTimestamp();
        $this->sendNotification('tarjeta_actualizada', $tarjeta);
    }

    public function deleted(Tarjeta $tarjeta): void
    {
        Log::info('Observer: Tarjeta eliminada con ID ' . $tarjeta->id);
        $this->updateLastModifiedTimestamp();
        $this->firebaseService->sendToTopic('actualizaciones', [
            'accion' => 'tarjeta_eliminada',
            'id' => $tarjeta->id,
            'id_lista' => (string)$tarjeta->id_padre,
        ]);
    }

    private function sendNotification(string $accion, Tarjeta $tarjeta): void
    {
        $payload = [
            'accion' => $accion,
            'id' => $tarjeta->id,
            'titulo' => $tarjeta->titulo ?? '',
            'id_lista' => (string)($tarjeta->id_padre ?? ''),
        ];
        //activarlas para mandar notificaciones push desde backend

        // $this->firebaseService->sendToTopic('actualizaciones_ios', $payload);
        // $this->firebaseService->sendToTopic('actualizaciones', $payload);
    }

    private function updateLastModifiedTimestamp(): void
    {   
        Cache::put('last_tarjeta_update_timestamp', now('America/Santiago')->toDateTimeString());
        Log::info('Marca de tiempo de última actualización guardada en caché.');
    }
}
