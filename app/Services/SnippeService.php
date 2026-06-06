<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SnippeService
{
    private string $apiKey;
    private string $webhookSecret;
    private string $baseUrl;
    private float  $commissionPercent;

    public function __construct()
    {
        $this->apiKey            = config('services.snippe.api_key', '');
        $this->webhookSecret     = config('services.snippe.webhook_secret', '');
        $this->baseUrl           = rtrim(config('services.snippe.base_url', 'https://api.snippe.sh/v1'), '/');
        $this->commissionPercent = (float) config('services.snippe.commission_percent', 5);
    }

    /**
     * Initiate a mobile-money STK push via Snippe.
     *
     * @return array{success: bool, reference?: string, message?: string}
     */
    public function initiatePayment(float $amount, string $phone, int $orderId, string $orderNumber): array
    {
        try {
            $http = Http::withHeaders([
                'Authorization'   => 'Bearer ' . $this->apiKey,
                'Idempotency-Key' => 'order-' . $orderId . '-' . time(),
                'Content-Type'    => 'application/json',
            ]);

            if (! app()->environment('production')) {
                $http = $http->withoutVerifying();
            }

            $response = $http->post($this->baseUrl . '/payments', [
                'type'         => 'mobile',
                'amount'       => [
                    'value'    => (int) round($amount),
                    'currency' => 'TZS',
                ],
                'customer'     => [
                    'phone' => $this->normalizePhone($phone),
                ],
                'metadata'     => [
                    'order_id'     => (string) $orderId,
                    'order_number' => $orderNumber,
                ],
                'callback_url' => url('/api/payments/callback'),
            ]);

            $body = $response->json();

            if ($response->successful() && isset($body['data']['reference'])) {
                return [
                    'success'   => true,
                    'reference' => $body['data']['reference'],
                ];
            }

            $message = $body['message'] ?? 'Payment initiation failed';
            Log::error('Snippe payment initiation failed', [
                'order_id' => $orderId,
                'status'   => $response->status(),
                'body'     => $body,
            ]);

            return ['success' => false, 'message' => $message];

        } catch (\Throwable $e) {
            Log::error('Snippe exception', ['order_id' => $orderId, 'error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Payment service unavailable'];
        }
    }

    /**
     * Verify the HMAC-SHA256 webhook signature sent by Snippe.
     * Reconstructed message format: "{timestamp}.{raw_body}"
     */
    public function verifyWebhookSignature(string $rawBody, string $signature, string $timestamp): bool
    {
        if (empty($this->webhookSecret)) {
            return true; // skip in dev when secret not configured
        }

        $expected = hash_hmac('sha256', $timestamp . '.' . $rawBody, $this->webhookSecret);
        return hash_equals($expected, $signature);
    }

    public function commissionPercent(): float
    {
        return $this->commissionPercent;
    }

    /** Convert local Tanzanian formats to E.164 (+255XXXXXXXXX). */
    private function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/[\s\-]/', '', $phone);

        if (str_starts_with($phone, '+255')) {
            return $phone;
        }
        if (str_starts_with($phone, '255')) {
            return '+' . $phone;
        }
        if (str_starts_with($phone, '0')) {
            return '+255' . substr($phone, 1);
        }

        return '+255' . $phone;
    }
}
