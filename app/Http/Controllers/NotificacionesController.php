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
        'token'        => 'required_if:audience,token|string|nullable',
        'topic'        => 'required_if:audience,topic|string|nullable',
        'click_action' => 'nullable|string|max:255',
        'image'        => 'nullable|url',
    ]);

    if ($v->fails()) {
        return response()->json(['ok' => false, 'errors' => $v->errors()], 422);
    }

    try {

                    $path = base_path(env('FIREBASE_CREDENTIALS'));
            if (!file_exists($path)) {
                return response()->json([
                    'ok' => false,
                    'error' => "❌ No existe el archivo en: $path",
                ], 500);
            }

        $serviceAccountPath = base_path(env('FIREBASE_CREDENTIALS'));
        $serviceAccount = json_decode(file_get_contents($serviceAccountPath), true);
        if (!is_array($serviceAccount)) {
        throw new \Exception('No se pudo cargar o decodificar el archivo FIREBASE_CREDENTIALS');
        }
        $projectId = $serviceAccount['project_id'];
        $clientEmail = $serviceAccount['client_email'];
        $privateKey = $serviceAccount['private_key'];

        // JWT para autenticación
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
        openssl_sign($jwtBase64Header.'.'.$jwtBase64Claim, $signature, $privateKey, 'sha256');
        if (!$signature) {
        throw new \Exception('No se pudo firmar el JWT, revisa el formato de la clave privada.');
        }
        $jwtSignature = rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');
        $jwt = $jwtBase64Header.'.'.$jwtBase64Claim.'.'.$jwtSignature;

        // Obtener token OAuth
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
            \Log::error('❌ No se pudo obtener token de acceso', ['response' => $response]);
            throw new \Exception('No se pudo obtener token de acceso de Google.');
        }

        // Validación y construcción de destino
        if ($request->audience === 'topic') {
            if (empty($request->topic)) {
                return response()->json(['ok' => false, 'error' => 'El campo "topic" no puede estar vacío cuando audience=topic'], 422);
            }
            $target = ['topic' => $request->topic];
        } else {
            if (empty($request->token)) {
                return response()->json(['ok' => false, 'error' => 'El campo "token" no puede estar vacío cuando audience=token'], 422);
            }
            $target = ['token' => $request->token];
        }

        // Construir payload FCM
      // Construir payload FCM FINAL COMPATIBLE
$payload = [
    'message' => array_merge($target, [

        // ---------------------------------------------------------
        // 1) 🔥 BLOQUE OBLIGATORIO PARA iOS cuando la app está cerrada
        // ---------------------------------------------------------
        'notification' => [
            'title' => $request->title ?? '',
            'body'  => $request->body ?? '',
        ],

        // ---------------------------------------------------------
        // 2) 🔥 SOLO DATA (Android usa esto SIEMPRE, iOS lo usa en foreground)
        // ---------------------------------------------------------
        'data' => [
            'title'      => $request->title ?? '',
            'body'       => $request->body ?? '',
            'screen'     => $request->click_action ?? 'centronoti',
            'channel_id' => 'canal_principal',
            'icon'       => 'ic_launcher',
            'color'      => '#1B255D',
        ],

        // ---------------------------------------------------------
        // 3) Configuración Android
        // ---------------------------------------------------------
        'android' => [
            'priority' => 'high',
            'notification' => [
                'channel_id' => 'canal_principal',
                'icon'       => 'ic_launcher',
                'color'      => '#1B255D',
            ],
        ],

        // ---------------------------------------------------------
        // 4) Configuración iOS (APNs)
        // ---------------------------------------------------------
        'apns' => [
            'headers' => [
                'apns-priority' => '10',
            ],
            'payload' => [
                'aps' => [
                    'alert' => [
                        'title' => $request->title ?? '',
                        'body'  => $request->body ?? '',
                    ],
                    'sound' => 'default',
                ],
            ],
        ],

    ]),
];


        // Enviar
        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";


                if (empty($projectId)) {
            \Log::error('❌ projectId vacío, revisa el service-account.json');
            return response()->json([
                'ok' => false,
                'error' => 'El project_id no fue encontrado en el archivo de credenciales.'
            ], 500);
        }
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $accessToken,
                'Content-Type: application/json; charset=UTF-8',
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