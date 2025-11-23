<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirebaseNotificationService
{
    private function getServiceAccount()
    {
        $projectId = env('FIREBASE_PROJECT_ID');
        $clientEmail = env('FIREBASE_CLIENT_EMAIL');
        $privateKey = env('FIREBASE_PRIVATE_KEY');
        $tokenUri = env('FIREBASE_TOKEN_URI', 'https://oauth2.googleapis.com/token');
        if (!$projectId || !$clientEmail || !$privateKey) {
            return null;
        }
        $privateKey = str_replace("\\n", "\n", $privateKey);
        return [
            'type' => 'service_account',
            'project_id' => $projectId,
            'private_key_id' => env('FIREBASE_PRIVATE_KEY_ID'),
            'private_key' => $privateKey,
            'client_email' => $clientEmail,
            'client_id' => env('FIREBASE_CLIENT_ID'),
            'token_uri' => $tokenUri,
        ];
    }

    /**
     * Generar Access Token para Firebase
     */
    private function getAccessToken()
    {
        $serviceAccount = $this->getServiceAccount();
        if (!$serviceAccount) {
            Log::warning('Firebase no configurado, omitida generación de access token');
            return null;
        }
        $now = time();
        $payload = [
            'iss' => $serviceAccount['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => $serviceAccount['token_uri'] ?? 'https://oauth2.googleapis.com/token',
            'exp' => $now + 3600,
            'iat' => $now
        ];

        $header = ['alg' => 'RS256', 'typ' => 'JWT'];
        $headerEncoded = $this->base64UrlEncode(json_encode($header));
        $payloadEncoded = $this->base64UrlEncode(json_encode($payload));
        $dataToSign = $headerEncoded . '.' . $payloadEncoded;

        openssl_sign($dataToSign, $signature, $serviceAccount['private_key'], 'SHA256');
        $signatureEncoded = $this->base64UrlEncode($signature);
        $jwt = $dataToSign . '.' . $signatureEncoded;

        $response = Http::asForm()->timeout(10)->post($serviceAccount['token_uri'] ?? 'https://oauth2.googleapis.com/token', [
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
            $serviceAccount = $this->getServiceAccount();
            if (!$serviceAccount) {
                Log::warning('Firebase no configurado, omitido envío de notificación');
                return false;
            }
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
            $serviceAccount = $this->getServiceAccount();
            if (!$serviceAccount) {
                return ['success' => false, 'invalid_token' => false];
            }
            $projectId = $serviceAccount['project_id'];
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