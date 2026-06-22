<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\VendorEarning;
use App\Services\FcmService;
use App\Services\PalmPesaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VendorOrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $orders = Order::where('vendor_id', $request->user()->id)
            ->with(['user', 'items.product.images', 'driver.user'])
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(20);

        return response()->json([
            'data' => OrderResource::collection($orders),
            'meta' => ['total' => $orders->total()],
        ]);
    }

    public function updateStatus(Request $request, Order $order): JsonResponse
    {
        abort_if(
            $order->vendor_id !== $request->user()->id,
            403, 'Unauthorized.'
        );

        $data = $request->validate([
            'status' => 'required|in:confirmed,preparing,ready,out_for_delivery,delivered,cancelled',
        ]);

        $order->update(['status' => $data['status']]);

        if ($data['status'] === 'confirmed') {
            $order->update(['confirmed_at' => now()]);
        }
        if ($data['status'] === 'preparing') {
            $order->update(['preparing_at' => now()]);
        }
        if ($data['status'] === 'ready') {
            $order->update(['ready_at' => now()]);
        }
        if ($data['status'] === 'delivered') {
            $order->update(['delivered_at' => now(), 'payment_status' => 'paid']);
            $fresh = $order->fresh();
            $this->ensureEarningExists($fresh);
            VendorEarning::where('order_id', $fresh->id)
                ->where('status', 'pending')
                ->update(['status' => 'settled']);
        }

        // Notify the customer about their order status change
        $statusMessages = [
            'confirmed'        => ['title' => '✅ Order Confirmed',       'body' => "Your order #{$order->order_number} has been confirmed!"],
            'preparing'        => ['title' => '👨‍🍳 Order Being Prepared',  'body' => "Your order #{$order->order_number} is being prepared."],
            'ready'            => ['title' => '📦 Order Ready',           'body' => "Your order #{$order->order_number} is ready for pickup/delivery."],
            'out_for_delivery' => ['title' => '🚴 Out for Delivery',      'body' => "Your order #{$order->order_number} is on its way!"],
            'delivered'        => ['title' => '🎉 Order Delivered',       'body' => "Your order #{$order->order_number} has been delivered. Enjoy!"],
            'cancelled'        => ['title' => '❌ Order Cancelled',       'body' => "Your order #{$order->order_number} has been cancelled."],
        ];

        if (isset($statusMessages[$data['status']])) {
            $msg = $statusMessages[$data['status']];
            app(FcmService::class)->notifyUser(
                userId: $order->user_id,
                title:  $msg['title'],
                body:   $msg['body'],
                data:   ['type' => 'order_status', 'order_id' => (string) $order->id],
            );
        }

        return response()->json(['data' => new OrderResource($order->fresh(['user', 'items.product.images', 'driver.user']))]);
    }

    private function ensureEarningExists(Order $order): void
    {
        if (VendorEarning::where('order_id', $order->id)->exists()) {
            return;
        }
        $pct        = app(PalmPesaService::class)->commissionPercent();
        $gross      = $order->subtotal;
        $commission = round($gross * ($pct / 100), 2);

        VendorEarning::create([
            'vendor_id'    => $order->vendor_id,
            'order_id'     => $order->id,
            'gross_amount' => $gross,
            'commission'   => $commission,
            'net_amount'   => round($gross - $commission, 2),
            'status'       => 'pending',
        ]);
    }
}
