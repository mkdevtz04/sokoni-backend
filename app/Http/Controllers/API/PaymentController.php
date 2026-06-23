<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\VendorEarning;
use App\Services\FcmService;
use App\Services\PalmPesaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct(private PalmPesaService $palmpesa) {}

    /**
     * PalmPesa webhook — called asynchronously when payment status changes.
     * Route: POST /api/payments/callback  (public, no auth)
     *
     * Expected payload:
     *   { "order_id": "PALMPESA17683440586334", "payment_status": "COMPLETED" }
     *
     * payment_status values: COMPLETED | FAILED | CANCELLED | PENDING
     */
    public function callback(Request $request): JsonResponse
    {
        $payload       = $request->json()->all();
        $palmOrderId   = $payload['order_id']       ?? null;
        $paymentStatus = strtoupper($payload['payment_status'] ?? '');

        Log::info('PalmPesa callback received', [
            'order_id'       => $palmOrderId,
            'payment_status' => $paymentStatus,
        ]);

        // Must return 200 immediately — PalmPesa will retry if we don't
        if (! $palmOrderId) {
            return response()->json(['message' => 'ok']);
        }

        $payment = Payment::where('reference', $palmOrderId)->first();

        if (! $payment) {
            Log::warning('PalmPesa callback: payment not found', ['reference' => $palmOrderId]);
            return response()->json(['message' => 'ok']);
        }

        match ($paymentStatus) {
            'COMPLETED'            => $this->handleCompleted($payment, $payload),
            'FAILED', 'CANCELLED'  => $this->handleFailed($payment, $payload),
            default                => null, // PENDING — do nothing, wait for next callback
        };

        return response()->json(['message' => 'ok']);
    }

    /**
     * App-triggered status check — called when webhook can't reach local dev server.
     * Route: POST /api/orders/{order}/verify-payment  (auth required)
     */
    public function verifyPayment(Request $request, Order $order): JsonResponse
    {
        if ($order->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ($order->payment_status === 'paid') {
            return response()->json(['message' => 'already_paid', 'payment_status' => 'paid']);
        }

        $payment = Payment::where('order_id', $order->id)
            ->where('method', 'mobile_money')
            ->latest()
            ->first();

        if (! $payment || ! $payment->reference) {
            return response()->json(['message' => 'no_reference', 'payment_status' => $order->payment_status]);
        }

        $status = $this->palmpesa->checkStatus($payment->reference);

        if ($status === 'COMPLETED') {
            $this->handleCompleted($payment, ['verified_by' => 'app_poll']);
            $order->refresh();
            return response()->json(['message' => 'paid', 'payment_status' => $order->payment_status]);
        }

        if (in_array($status, ['FAILED', 'CANCELLED'])) {
            $this->handleFailed($payment, ['verified_by' => 'app_poll']);
            return response()->json(['message' => 'failed', 'payment_status' => 'failed']);
        }

        return response()->json(['message' => 'pending', 'payment_status' => 'pending']);
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

            // Notify customer — payment received
            app(FcmService::class)->notifyUser(
                userId: $order->user_id,
                title:  '✅ Payment Confirmed',
                body:   "Payment for order {$order->order_number} was successful!",
                data:   ['type' => 'payment_confirmed', 'order_id' => (string) $order->id],
            );

            // Notify vendor — order paid and confirmed
            app(FcmService::class)->notifyUser(
                userId: $order->vendor_id,
                title:  '💰 Payment Received',
                body:   "Order {$order->order_number} has been paid. Ready to prepare!",
                data:   ['type' => 'order_paid', 'order_id' => (string) $order->id],
            );
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

            $order->update(['status' => 'cancelled', 'payment_status' => 'failed']);

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

        $commissionPct = $this->palmpesa->commissionPercent();
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
