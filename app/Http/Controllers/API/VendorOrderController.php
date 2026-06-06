<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\VendorEarning;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VendorOrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $orders = Order::where('vendor_id', $request->user()->id)
            ->with(['user', 'items.product.images'])
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
        if ($data['status'] === 'delivered') {
            $order->update(['delivered_at' => now(), 'payment_status' => 'paid']);
            $this->ensureEarningExists($order->fresh());
        }

        return response()->json(['data' => new OrderResource($order->fresh(['user', 'items.product.images']))]);
    }

    private function ensureEarningExists(Order $order): void
    {
        if (VendorEarning::where('order_id', $order->id)->exists()) {
            return;
        }
        $pct        = (float) config('services.snippe.commission_percent', 5);
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
