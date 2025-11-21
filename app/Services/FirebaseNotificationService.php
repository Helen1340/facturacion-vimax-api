<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirebaseNotificationService
{
    // 👇 Pon aquí tus credenciales de Firebase (las sacas del JSON que descargaste)
    // Ve a Firebase Console → Project Settings → Service Accounts → Generate new private key
    private $serviceAccount = [
        "type" => "service_account",
        "project_id" => "vi-max-ec031",
        "private_key_id" => "aa21d54e1a76f438c473aada0ed5e5bbe86e0720",
        "private_key" => "-----BEGIN PRIVATE KEY-----\nMIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCsljayjvh0X3n1\nvQ8pXXCGemtG1lpOe61hWuU7+5aoAC1C2CWX1DES/c/sScjixuuaVUYAAQVgKpzf\n3W0XaJHFwNiUNgOdSWlFrpW68zZBZnuwRogdnzhJtqa7i24P6WebZfqmz7jZxQB+\ne4ISVjd61J8YYj/DGxsmGzIDraUUf/YwFzavDXaGaqw6XXHUirUg6EwG+iBCfWhz\nD0HlBxa2Gh4vjK3oKaNmnjMctilwylfxvSWidj7nFYeyIm0hy3UXP/IPei64uSSk\nzoxBzlYb35S0w3LNItOKgaynWj0wDNi8/AQ04WcR253VegSsMjZB01OT6ajoyrN5\nVJTHL8DpAgMBAAECggEAA2YjyTgPB9teWihT6/1CKJ3bhBjCN3fmAgwMRiEGIQR8\nNQwEz9o5m12FEAA4glHcr5c6F+jX56J94Nv6KJW87Y4m8yKjXIBxgAeIZ/cKBKH4\nHJBuYMH4ix1w0sepU7Yls0NqsIjDA9lGcWyX3aoAcU0jyH3H3R0eV42TJQwmkLJN\nATHf9t230JV7RksL6XbNmh31G4xDUu3JfZ4q37fLhzfEZHuCLHupms4v6ojkpdGG\nXiadTRIXVHSn6JK2aszikPTBL8Rpab5wBTFtm+MgNJnKspT6buytcEYM8FLdVWT1\n6YfjblhkXYJfL15DEQOWXfIrKYSmNv1qHdBZ4JNBwQKBgQDenNC98ymKFLcKKwbB\nO15liUJ6utnB/YXBqpWKaOn7QvnTSAnyuiaD1jEoPW3VhPCkm8ME6q1c72dtOfCB\nn+8z2fg5dm+TzdFqKhTtqZkOR9N3KdlantQH0PXvla3eOGETv9MHOIga1sfoPC6t\nbfj4WhaqBbNl28ukVQBdejgQQQKBgQDGeKnmT4spz+Ep7Tn47sfSWyZ4qa2SJ8GY\nRgS/N9FkNIQPlYIWsNvPRG/GeZrI6reOqFVTEWgQA7trTkefp+o2+USvQ2FLOmdA\nHD4TdDABlDtT0nbKFCeI85Zj6kAz6MMJ2y0LlWMhuQi+3W00Mp1tbyv8AN+uapiD\nLwu51OuGqQKBgFa3LVUg+KhyI08RraLt9nqE+mGGkbbQhB8JzRDKh4K590lHQaDM\nxJ4MfL+ZFkTbcUYd6tzqcbfHBjn1HOvRmkNPgDMaIKKpxQ6e7+IYc6etcQF1StbT\nEfMtge/fFYh/28juq0yfx9z0l5CuiNxD/3z20udOKzDdarlL50WCi35BAoGAU1dY\nAN5mEai5ZGG5dk7OmgasoP6fZEBNiyPb2nAV/X4P9ntRpTWfF+olBbMKzFPDwKPv\nMHKewBrRGL5GVUStlWgW74Hb2TstID670m93uTIFt60pNLJB58Bh5pL3YtTbEch3\noeWZOw/4HC3fLYwTj2Cfl7LGOveIE44t3lsKjAECgYEAtowULNBJiH0kc/Pb8N2o\nAEQA+Atyn8IPumZ2mYMJFMBfMlZxg5FvNHc8B8sqNFJ15CorWuzUYzp7hz+aEv0v\nP9agsqdSa0mPorolxvetRReRQuGMtJYxJDcmtg50YdlDT0dMgruwxQUYfsqTH29c\nPi5b3CfbTvdjZAz5SVV3LtE=\n-----END PRIVATE KEY-----\n",
        "client_email" => "firebase-adminsdk-xxxxx@vi-max-ec031.iam.gserviceaccount.com",
        "client_id" => "112872972029936767965",
        "token_uri" => "https://oauth2.googleapis.com/token",
    ];

    /**
     * Generar Access Token para Firebase
     */
    private function getAccessToken()
    {
        $now = time();
        $payload = [
            'iss' => $this->serviceAccount['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => 'https://oauth2.googleapis.com/token',
            'exp' => $now + 3600,
            'iat' => $now
        ];

        $header = ['alg' => 'RS256', 'typ' => 'JWT'];
        $headerEncoded = $this->base64UrlEncode(json_encode($header));
        $payloadEncoded = $this->base64UrlEncode(json_encode($payload));
        $dataToSign = $headerEncoded . '.' . $payloadEncoded;

        openssl_sign($dataToSign, $signature, $this->serviceAccount['private_key'], 'SHA256');
        $signatureEncoded = $this->base64UrlEncode($signature);
        $jwt = $dataToSign . '.' . $signatureEncoded;

        $response = Http::asForm()->timeout(10)->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt
        ]);

        $tokenData = $response->json();
        return $tokenData['access_token'] ?? null;
    }

    private function base64UrlEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * 🔥 Enviar notificación cuando cambia el status del usuario
     */
    public function sendUserStatusNotification($userId, $newStatus)
    {
        try {
            // 1. Obtener token del usuario
            $token = DB::table('device_tokens')
                ->where('user_id', $userId)
                ->where('is_active', true)
                ->value('fcm_token');

            if (!$token) {
                Log::info("Usuario $userId sin token FCM activo");
                return false;
            }

            // 2. Obtener access token
            $accessToken = $this->getAccessToken();
            if (!$accessToken) {
                Log::error("No se pudo generar access token");
                return false;
            }

            // 3. Preparar mensaje según status
            if ($newStatus === 'Active') {
                $title = '¡Cuenta Activada!';
                $body = 'Tu cuenta ha sido activada exitosamente.';
                $action = 'reload';
            } else {
                $title = 'Cuenta Desactivada';
                $body = 'Tu cuenta ha sido desactivada.';
                $action = 'logout';
            }

            $data = [
                'type' => 'user_status',
                'status' => $newStatus,
                'action' => $action,
                'user_id' => (string)$userId
            ];

            // 4. Enviar notificación
            $result = $this->sendToToken($token, $title, $body, $data, $accessToken);

            if ($result['success']) {
                Log::info("Notificación enviada al usuario $userId");
                return true;
            }

            // Si el token es inválido, desactivarlo
            if ($result['invalid_token']) {
                DB::table('device_tokens')
                    ->where('user_id', $userId)
                    ->where('fcm_token', $token)
                    ->update(['is_active' => false]);
                Log::info("Token inválido desactivado para usuario $userId");
            }

            return false;

        } catch (\Exception $e) {
            Log::error("Error en sendUserStatusNotification: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Enviar notificación a un token específico
     */
    private function sendToToken($fcmToken, $title, $body, $data, $accessToken)
    {
        try {
            $projectId = $this->serviceAccount['project_id'];
            $fcmUrl = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

            $payload = [
                'message' => [
                    'token' => $fcmToken,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                    'data' => $data,
                    'webpush' => [
                        'notification' => [
                            'icon' => '/assets/icon.png',
                            'badge' => '/assets/badge.png'
                        ],
                        'fcm_options' => [
                            'link' => '/'
                        ]
                    ]
                ]
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->timeout(10)->post($fcmUrl, $payload);

            if ($response->successful()) {
                return ['success' => true, 'invalid_token' => false];
            }

            $result = $response->json();
            $isInvalid = isset($result['error']['details'][0]['errorCode']) &&
                $result['error']['details'][0]['errorCode'] === 'UNREGISTERED';

            Log::error("Error FCM: " . json_encode($result));
            return ['success' => false, 'invalid_token' => $isInvalid];

        } catch (\Exception $e) {
            Log::error("Error enviando a token: " . $e->getMessage());
            return ['success' => false, 'invalid_token' => false];
        }
    }
}