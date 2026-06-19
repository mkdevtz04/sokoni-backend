<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KilakonaService
{
    private string $apiKey;
    private string $apiSecret;
    private string $senderId;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey    = config('services.kilakona.api_key',    '');
        $this->apiSecret = config('services.kilakona.api_secret', '');
        $this->senderId  = config('services.kilakona.sender_id',  'SOKONI');
        $this->baseUrl   = config('services.kilakona.base_url',   '');
    }

    public function isConfigured(): bool
    {
        return $this->apiKey !== '' && $this->apiSecret !== '' && $this->baseUrl !== '';
    }

    /**
     * Send an SMS message.
     *
     * @param  string|array  $phone  Single phone or comma-separated / array
     * @param  string        $message
     * @return bool
     */
    public function send(string|array $phone, string $message): bool
    {
        if (! $this->isConfigured()) {
            throw new \RuntimeException(
                'SMS service not configured. Add KILAKONA_API_KEY, KILAKONA_API_SECRET, and KILAKONA_BASE_URL to your .env file.'
            );
        }

        $contacts = is_array($phone)
            ? implode(',', array_map([$this, 'formatPhone'], $phone))
            : $this->formatPhone($phone);

        try {
            $http = Http::withHeaders([
                'Content-Type' => 'application/json',
                'api_key'      => $this->apiKey,
                'api_secret'   => $this->apiSecret,
            ]);

            // XAMPP/Windows dev machines lack a CA bundle — skip SSL verify locally
            if (! app()->environment('production')) {
                $http = $http->withoutVerifying();
            }

            $response = $http->post($this->baseUrl, [
                'senderId'    => $this->senderId,
                'messageType' => 'text',
                'message'     => $message,
                'contacts'    => $contacts,
            ]);

            if ($response->successful()) {
                Log::info('Kilakona SMS sent', [
                    'contacts' => $contacts,
                    'response' => $response->json(),
                ]);
                return true;
            }

            Log::error('Kilakona SMS failed', [
                'contacts' => $contacts,
                'status'   => $response->status(),
                'body'     => $response->body(),
            ]);
            return false;

        } catch (\Throwable $e) {
            Log::error('Kilakona SMS exception', [
                'contacts' => $contacts,
                'error'    => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Convert any Tanzanian phone format to international (255XXXXXXXXX).
     * Handles: 0712345678 → 255712345678
     *          +255712345678 → 255712345678
     *          255712345678 → 255712345678
     */
    public function formatPhone(string $phone): string
    {
        $phone = preg_replace('/\s+/', '', $phone); // strip spaces

        if (str_starts_with($phone, '+255')) {
            return ltrim($phone, '+');
        }

        if (str_starts_with($phone, '255')) {
            return $phone;
        }

        if (str_starts_with($phone, '0')) {
            return '255' . substr($phone, 1);
        }

        // Already a bare number without country code prefix
        return '255' . $phone;
    }
}
