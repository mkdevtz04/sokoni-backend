<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\VendorEarning;
use App\Services\SnippeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct(private SnippeService $snippe) {}

    /**
     * Snippe webhook — called when a payment succeeds or fails.
     * Route: POST /api/payments/callback  (public, no auth)
     */
    public function callback(Request $request): JsonResponse
    {
        $rawBody   = $request->getContent();
        $signature = $request->header('X-Webhook-Signature', '');
        $timestamp = $request->header('X-Webhook-Timestamp', '');

        if (! $this->snippe->verifyWebhookSignature($rawBody, $signature, $timestamp)) {
            Log::warning('Snippe webhook: invalid signature');
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $payload = $request->json()->all();
        $type    = $payload['type'] ?? '';
        $data    = $payload['data'] ?? [];
        $ref     = $data['reference'] ?? null;

        Log::info('Snippe webhook received', ['type' => $type, 'reference' => $ref]);

        if (! $ref) {
            return response()->json(['message' => 'ok']);
        }

        $payment = Payment::where('reference', $ref)->first();

        if (! $payment) {
            Log::warning('Snippe webhook: payment not found', ['reference' => $ref]);
            return response()->json(['message' => 'ok']);
        }

        match ($type) {
            'payment.completed' => $this->handleCompleted($payment, $data),
            'payment.failed'    => $this->handleFailed($payment, $data),
            default             => null,
        };

        return response()->json(['message' => 'ok']);
    }

    private function handleCompleted(Payment $payment, array $data): void
    {
        if ($payment->status === 'completed') {
            return; // idempotency — already processed
        }

        DB::transaction(function () use ($payment, $data) {
            $payment->update([
                'status'   => 'completed',
                'metadata' => array_merge($payment->metadata ?? [], $data),
            ]);

            $order = Order::with('items')->find($payment->order_id);
            if (! $order) {
                return;
            }

            $order->update([
                'payment_status' => 'paid',
                'status'         => 'confirmed',
                'confirmed_at'   => now(),
            ]);

            $this->recordVendorEarning($order);
        });
    }

    private function handleFailed(Payment $payment, array $data): void
    {
        if (in_array($payment->status, ['failed', 'completed'])) {
            return;
        }

        DB::transaction(function () use ($payment, $data) {
            $payment->update([
                'status'   => 'failed',
                'metadata' => array_merge($payment->metadata ?? [], $data),
            ]);

            $order = Order::with('items.product')->find($payment->order_id);
            if (! $order) {
                return;
            }

            $order->update(['status' => 'cancelled']);

            // Restore stock
            foreach ($order->items as $item) {
                if ($item->product) {
                    $item->product->increment('stock', $item->quantity);
                    $item->product->decrement('total_orders');
                }
            }
        });
    }

    private function recordVendorEarning(Order $order): void
    {
        if (VendorEarning::where('order_id', $order->id)->exists()) {
            return;
        }

        $commissionPct = $this->snippe->commissionPercent();
        $gross         = $order->subtotal;
        $commission    = round($gross * ($commissionPct / 100), 2);
        $net           = round($gross - $commission, 2);

        VendorEarning::create([
            'vendor_id'    => $order->vendor_id,
            'order_id'     => $order->id,
            'gross_amount' => $gross,
            'commission'   => $commission,
            'net_amount'   => $net,
            'status'       => 'pending',
        ]);
    }
}
