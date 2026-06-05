<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VendorOrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $orders = Order::where('vendor_id', $request->user()->vendorProfile?->id)
            ->with(['user', 'items'])
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
            $order->vendor_id !== $request->user()->vendorProfile?->id,
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
        }

        return response()->json(['data' => new OrderResource($order->fresh(['user', 'items']))]);
    }
}
