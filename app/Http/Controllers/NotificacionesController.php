<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\RawMessageFromArray;
use Illuminate\Support\Facades\Validator;

class NotificacionesController extends Controller
{
    protected function messaging()
    {
        $factory = (new Factory)
            ->withServiceAccount(config('firebase.credentials.file', env('FIREBASE_CREDENTIALS')));

        return $factory->createMessaging();
    }

    public function enviar(Request $request)
{
    $v = Validator::make($request->all(), [
        'title'        => 'required|string|max:120',
        'body'         => 'required|string|max:500',
        'audience'     => 'required|string|in:token,topic',
        'token'        => 'required_if:audience,token|string',
        'topic'        => 'required_if:audience,topic|string',
        'click_action' => 'nullable|string|max:255',
        'image'        => 'nullable|url',
    ]);

    if ($v->fails()) {
        return response()->json(['ok' => false, 'errors' => $v->errors()], 422);
    }

    try {
        // 🔥 Cargar credenciales del mismo proyecto (service-account.json)
        $serviceAccount = json_decode(file_get_contents(env('FIREBASE_CREDENTIALS')), true);

        $projectId = $serviceAccount['project_id'];
        $clientEmail = $serviceAccount['client_email'];
        $privateKey = $serviceAccount['private_key'];

        // 🧩 Generar JWT para autenticación con FCM v1
        $now = time();
        $jwtHeader = ['alg' => 'RS256', 'typ' => 'JWT'];
        $jwtClaim = [
            'iss' => $clientEmail,
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => $now,
            'exp' => $now + 3600,
        ];

        $jwtBase64Header = rtrim(strtr(base64_encode(json_encode($jwtHeader)), '+/', '-_'), '=');
        $jwtBase64Claim  = rtrim(strtr(base64_encode(json_encode($jwtClaim)), '+/', '-_'), '=');
        $signature = '';
        openssl_sign($jwtBase64Header.'.'.$jwtBase64Claim, $signature, $privateKey, 'sha256');
        $jwtSignature = rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');
        $jwt = $jwtBase64Header.'.'.$jwtBase64Claim.'.'.$jwtSignature;

        // 🧠 Obtener token de acceso de Google OAuth
        $ch = curl_init('https://oauth2.googleapis.com/token');
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
            CURLOPT_POSTFIELDS => http_build_query([
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion'  => $jwt,
            ]),
        ]);
        $response = json_decode(curl_exec($ch), true);
        curl_close($ch);
        $accessToken = $response['access_token'] ?? null;

        if (!$accessToken) {
            throw new \Exception('No se pudo obtener token de acceso de Google.');
        }

        // 📨 Construir payload FCM
        $target = $request->audience === 'topic'
            ? [ 'topic' => $request->topic ]
            : [ 'token' => $request->token ];

        $payload = [
            'message' => array_merge($target, [
                'notification' => [
                    'title' => $request->title,
                    'body'  => $request->body,
                ],
                'android' => [
                    'priority' => 'high',
                    'notification' => [
                        'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                        'icon' => 'ic_launcher',
                        'color' => '#1B255D',
                    ],
                ],
                'data' => [
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                    'screen'       => $request->click_action ?? '/centro-noti',
                ],
            ]),
        ];

        // 🚀 Enviar a la API REST v1 de FCM
        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $accessToken,
                'Content-Type: application/json; UTF-8',
            ],
            CURLOPT_POSTFIELDS => json_encode($payload),
        ]);

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return response()->json([
            'ok' => $httpCode === 200,
            'http_code' => $httpCode,
            'response' => json_decode($result, true),
            'payload' => $payload,
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'ok' => false,
            'error' => $e->getMessage(),
        ], 500);
    }
}

}