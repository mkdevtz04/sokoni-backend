<?php

namespace App\Services;

use App\Models\DeviceToken;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FcmService
{
    private string $projectId;
    private string $fcmUrl;
    private ?array $credentials = null;

    public function __construct()
    {
        $this->projectId = config('services.fcm.project_id', '');
        $this->fcmUrl    = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

        $path = config('services.fcm.credentials_path')
            ?: storage_path('app/firebase/sokoniapp-fcd93-firebase-adminsdk.json');
        if ($path && file_exists($path)) {
            $this->credentials = json_decode(file_get_contents($path), true);
        }
    }

    /**
     * Send a push notification to a single user (all their registered devices).
     */
    public function notifyUser(int $userId, string $title, string $body, array $data = []): void
    {
        if (! $this->credentials) return;

        $tokens = DeviceToken::where('user_id', $userId)->pluck('token')->toArray();

        foreach ($tokens as $token) {
            $this->sendToToken($token, $title, $body, $data);
        }
    }

    /**
     * Send a push notification to multiple users at once.
     */
    public function notifyUsers(array $userIds, string $title, string $body, array $data = []): void
    {
        if (! $this->credentials || empty($userIds)) return;

        $tokens = DeviceToken::whereIn('user_id', $userIds)->pluck('token')->toArray();

        foreach ($tokens as $token) {
            $this->sendToToken($token, $title, $body, $data);
        }
    }

    // ── Internal ──────────────────────────────────────────────────────────────

    private function sendToToken(string $token, string $title, string $body, array $data): void
    {
        try {
            $accessToken = $this->getAccessToken();

            // FCM v1 data values must all be strings
            $stringData = array_map('strval', $data);

            $payload = [
                'message' => [
                    'token'        => $token,
                    'notification' => [
                        'title' => $title,
                        'body'  => $body,
                    ],
                    'data'    => $stringData,
                    'android' => [
                        'priority'     => 'high',
                        'notification' => [
                            'sound'      => 'default',
                            'channel_id' => 'sokoni_orders',
                        ],
                    ],
                    'apns' => [
                        'headers' => ['apns-priority' => '10'],
                        'payload' => [
                            'aps' => [
                                'sound' => 'default',
                                'badge' => 1,
                            ],
                        ],
                    ],
                ],
            ];

            $response = Http::when(app()->isLocal(), fn ($h) => $h->withoutVerifying())
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type'  => 'application/json',
                ])->post($this->fcmUrl, $payload);

            if (! $response->successful()) {
                $errorStatus = $response->json()['error']['status'] ?? '';

                // Clean up tokens that FCM says are no longer valid
                if (in_array($errorStatus, ['INVALID_ARGUMENT', 'NOT_FOUND', 'UNREGISTERED'])) {
                    DeviceToken::where('token', $token)->delete();
                }

                Log::warning('FCM send failed', [
                    'status' => $response->status(),
                    'body'   => $response->json(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('FCM exception', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Get a valid OAuth2 access token for the FCM HTTP v1 API.
     * Cached for 58 minutes (tokens last 60 min).
     */
    private function getAccessToken(): string
    {
        return Cache::remember('fcm_v1_access_token', 3480, function () {
            $now = time();

            $header  = $this->b64url(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
            $claims  = $this->b64url(json_encode([
                'iss'   => $this->credentials['client_email'],
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                'aud'   => 'https://oauth2.googleapis.com/token',
                'iat'   => $now,
                'exp'   => $now + 3600,
            ]));

            $signingInput = "$header.$claims";
            openssl_sign($signingInput, $signature, $this->credentials['private_key'], 'SHA256');
            $jwt = "$signingInput." . $this->b64url($signature);

            $response = Http::when(app()->isLocal(), fn ($h) => $h->withoutVerifying())
                ->asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion'  => $jwt,
            ]);

            if (! $response->successful()) {
                throw new \RuntimeException('FCM OAuth token fetch failed: ' . $response->body());
            }

            return $response->json()['access_token'];
        });
    }

    private function b64url(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
