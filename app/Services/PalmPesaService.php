<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PalmPesaService
{
    private string $apiToken;
    private string $baseUrl;
    private float  $commissionPercent;

    public function __construct()
    {
        $this->apiToken          = config('services.palmpesa.api_token', '');
        $this->baseUrl           = rtrim(config('services.palmpesa.base_url', 'https://palmpesa.drmlelwa.co.tz'), '/');
        $this->commissionPercent = (float) config('services.palmpesa.commission_percent', 5);
    }

    /**
     * Initiate a mobile-money STK push via PalmPesa.
     *
     * @return array{success: bool, reference?: string, message?: string}
     */
    public function initiatePayment(
        float  $amount,
        string $phone,
        int    $orderId,
        string $orderNumber,
        string $customerName  = 'Customer',
        string $customerEmail = ''
    ): array {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiToken,
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
            ])->withoutVerifying()->post($this->baseUrl . '/api/palmpesa/initiate', [
                'name'           => $customerName,
                'email'          => $customerEmail ?: 'noreply@sokoni.co.tz',
                'phone'          => $this->normalizePhone($phone),
                'amount'         => (int) round($amount),
                'transaction_id' => $orderNumber,
                'address'        => 'Tanzania',
                'postcode'       => '00255',
                'callback_url'   => url('/api/payments/callback'),
            ]);

            $body = $response->json();

            Log::info('PalmPesa initiation response', [
                'order_id' => $orderId,
                'status'   => $response->status(),
                'body'     => $body,
            ]);

            if ($response->successful() && isset($body['order_id'])) {
                return [
                    'success'   => true,
                    'reference' => $body['order_id'], // e.g. "PALMPESA17682869972044"
                ];
            }

            $message = $body['message'] ?? 'Payment initiation failed';
            Log::error('PalmPesa payment initiation failed', [
                'order_id' => $orderId,
                'status'   => $response->status(),
                'body'     => $body,
            ]);

            return ['success' => false, 'message' => $message];

        } catch (\Throwable $e) {
            Log::error('PalmPesa exception', ['order_id' => $orderId, 'error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Payment service unavailable'];
        }
    }

    /**
     * Check the current status of a payment by its PalmPesa order_id reference.
     * Returns 'COMPLETED' | 'FAILED' | 'PENDING' | null (unknown/error).
     */
    public function checkStatus(string $reference): ?string
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiToken,
                'Accept'        => 'application/json',
            ])->withoutVerifying()->get($this->baseUrl . '/api/palmpesa/orders/' . $reference);

            if (! $response->successful()) {
                Log::warning('PalmPesa status check failed', [
                    'reference' => $reference,
                    'status'    => $response->status(),
                ]);
                return null;
            }

            $body = $response->json();
            Log::info('PalmPesa status check', ['reference' => $reference, 'body' => $body]);

            // Normalise whatever field the API returns
            $status = strtoupper(
                $body['payment_status'] ?? $body['status'] ?? $body['transaction_status'] ?? ''
            );

            return $status ?: null;

        } catch (\Throwable $e) {
            Log::error('PalmPesa status check exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    public function commissionPercent(): float
    {
        return $this->commissionPercent;
    }

    /** Convert Tanzanian phone to 06/07 format required by PalmPesa (no + or country code). */
    private function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/[\s\-]/', '', $phone);

        if (str_starts_with($phone, '+255')) {
            return '0' . substr($phone, 4);
        }
        if (str_starts_with($phone, '255')) {
            return '0' . substr($phone, 3);
        }
        if (str_starts_with($phone, '0')) {
            return $phone;
        }

        return '0' . $phone;
    }
}
