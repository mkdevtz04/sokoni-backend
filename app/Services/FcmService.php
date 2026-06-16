<?php

namespace App\Services;

use App\Models\DeviceToken;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FcmService
{
    private string $serverKey;
    private string $fcmUrl = 'https://fcm.googleapis.com/fcm/send';

    public function __construct()
    {
        $this->serverKey = config('services.fcm.server_key', '');
    }

    /**
     * Send a push notification to a specific user.
     * Silently skips if FCM is not configured or user has no tokens.
     */
    public function notifyUser(int $userId, string $title, string $body, array $data = []): void
    {
        if (empty($this->serverKey)) {
            return; // FCM not configured yet
        }

        $tokens = DeviceToken::where('user_id', $userId)
            ->pluck('token')
            ->toArray();

        if (empty($tokens)) {
            return;
        }

        $this->send($tokens, $title, $body, $data);
    }

    /**
     * Send a push notification to multiple users at once.
     */
    public function notifyUsers(array $userIds, string $title, string $body, array $data = []): void
    {
        if (empty($this->serverKey) || empty($userIds)) {
            return;
        }

        $tokens = DeviceToken::whereIn('user_id', $userIds)
            ->pluck('token')
            ->toArray();

        if (empty($tokens)) {
            return;
        }

        $this->send($tokens, $title, $body, $data);
    }

    private function send(array $tokens, string $title, string $body, array $data): void
    {
        try {
            $payload = [
                'registration_ids' => $tokens,
                'notification'     => [
                    'title' => $title,
                    'body'  => $body,
                    'sound' => 'default',
                ],
                'data'    => $data,
                'priority'=> 'high',
            ];

            $response = Http::withHeaders([
                'Authorization' => 'key=' . $this->serverKey,
                'Content-Type'  => 'application/json',
            ])->withoutVerifying()->post($this->fcmUrl, $payload);

            if (! $response->successful()) {
                Log::warning('FCM send failed', [
                    'status' => $response->status(),
                    'body'   => $response->json(),
                ]);
                return;
            }

            // Clean up tokens FCM reports as invalid/unregistered
            $result = $response->json();
            if (($result['failure'] ?? 0) > 0) {
                $results = $result['results'] ?? [];
                foreach ($results as $i => $r) {
                    if (isset($r['error']) && in_array($r['error'], ['InvalidRegistration', 'NotRegistered'])) {
                        DeviceToken::where('token', $tokens[$i])->delete();
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::error('FCM exception', ['error' => $e->getMessage()]);
        }
    }
}
