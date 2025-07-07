<?php

namespace App\Services;

use Google_Client;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirebaseNotificationService
{
    protected ?string $accessToken = null;

    public function __construct()
    {
        try {
            $client = new Google_Client();
            $client->setAuthConfig(storage_path('app/firebase/espacio-mayor-7a026-firebase-adminsdk-fbsvc-c92ee6eaca.json'));
            $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

            // fetchAccessTokenWithAssertion puede usar una caché, es eficiente.
            $tokenData = $client->fetchAccessTokenWithAssertion();
            $this->accessToken = $tokenData['access_token'] ?? null;
        } catch (\Exception $e) {
            Log::error('❌ Error al autenticar con Firebase', ['message' => $e->getMessage()]);
        }
    }

    public function sendToTopic(string $topic, array $data)
    {
        if (is_null($this->accessToken)) {
            Log::error('❌ No se pudo enviar notificación porque el token de acceso de Firebase es nulo.');
        }

        $projectId = env('FIREBASE_PROJECT_ID');
        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        $stringData = array_map('strval', $data);

        $payload = [
            'message' => [
                'topic' => $topic,
                'data' => $stringData,
                'apns' => [
                    'headers' => [
                        'apns-priority' => '10',
                        'apns-push-type' => 'alert',
                    ],
                    'payload' => [
                        'aps' => [
                            'alert' => [
                                'title' => 'hay una nueva tarjeta',
                                'body' => 'Se actualizó la lista #' . ($data['id_lista'] ?? '¿?') . '.',
                            ],
                            'sound' => 'default',
                        ],
                    ],
                ],
                'android' => [
                    'priority' => 'high',
                ],
            ],
        ];

        try {
            $response = Http::withToken($this->accessToken)
                ->post($url, $payload);

            if ($response->successful()) {
                Log::info('✅ Notificación enviada correctamente al topic: ' . $topic, $response->json());
            } else {
                Log::error('❌ Error al enviar notificación al topic: ' . $topic, [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
            return $response;
        } catch (\Exception $e) {
            Log::error('❌ Fallo crítico en la petición a FCM', ['message' => $e->getMessage()]);
            return null;
        }
    }
}


// 'android' => [
//                 'priority' => 'high',
//                 'notification' => [
//                     'title' => 'Actualización disponible',
//                     'body' => 'Se actualizó la lista #'.($data['id_lista'] ?? '¿?').'.',
//                 ],
//             ],