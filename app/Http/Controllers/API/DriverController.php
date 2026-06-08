<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\Order;
use App\Services\FcmService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    private function driver(Request $request): ?Driver
    {
        return Driver::where('user_id', $request->user()->id)->first();
    }

    public function profile(Request $request): JsonResponse
    {
        $driver = $this->driver($request);
        if (! $driver) {
            return response()->json(['message' => 'Driver profile not found'], 404);
        }
        $user = $request->user();
        return response()->json(['data' => [
            'id'               => $driver->id,
            'status'           => $driver->status,
            'rating'           => (float) ($driver->rating ?? 0),
            'total_deliveries' => (int) ($driver->total_deliveries ?? 0),
            'user'             => [
                'id'              => $user->id,
                'first_name'      => $user->first_name,
                'last_name'       => $user->last_name,
                'full_name'       => $user->full_name,
                'phone'           => $user->phone,
                'email'           => $user->email,
                'profile_picture' => $user->profile_picture,
            ],
        ]]);
    }

    /** Driver's completed delivery history */
    public function history(Request $request): JsonResponse
    {
        $driver = $this->driver($request);
        if (! $driver) return response()->json(['data' => []]);

        $orders = Order::with(['vendorProfile', 'deliveryAddress'])
            ->where('driver_id', $driver->id)
            ->where('status', 'delivered')
            ->latest()
            ->paginate(20);

        return response()->json([
            'data' => $this->formatOrders($orders),
            'meta' => [
                'total'        => $orders->total(),
                'current_page' => $orders->currentPage(),
                'last_page'    => $orders->lastPage(),
            ],
        ]);
    }

    /** Update driver's own user profile (name, email) */
    public function updateProfile(Request $request): JsonResponse
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:80',
            'last_name'  => 'required|string|max:80',
            'email'      => 'nullable|email|max:180|unique:users,email,' . $request->user()->id,
        ]);

        $request->user()->update($data);

        return response()->json(['user' => $request->user()->fresh()]);
    }

    /** Deliveries available to accept (status = ready, no driver assigned) */
    public function available(Request $request): JsonResponse
    {
        $orders = Order::with(['vendorProfile', 'deliveryAddress', 'items'])
            ->where('status', 'ready')
            ->whereNull('driver_id')
            ->latest()
            ->get();

        return response()->json(['data' => $this->formatOrders($orders)]);
    }

    /** Deliveries assigned to this driver */
    public function myDeliveries(Request $request): JsonResponse
    {
        $driver = $this->driver($request);
        if (! $driver) return response()->json(['data' => []]);

        $orders = Order::with(['vendorProfile', 'deliveryAddress', 'user', 'items'])
            ->where('driver_id', $driver->id)
            ->whereIn('status', ['out_for_delivery', 'ready'])
            ->latest()
            ->get();

        return response()->json(['data' => $this->formatOrders($orders)]);
    }

    /** Accept a delivery */
    public function accept(Request $request, Order $order): JsonResponse
    {
        $driver = $this->driver($request);
        if (! $driver) return response()->json(['message' => 'Not a driver'], 403);

        if ($order->status !== 'ready' || $order->driver_id !== null) {
            return response()->json(['message' => 'Order not available for pickup'], 422);
        }

        $order->update([
            'driver_id' => $driver->id,
            'status'    => 'out_for_delivery',
        ]);

        FcmService::notifyUser(
            $order->user_id,
            '🚗 Order On the Way',
            "Your order #{$order->order_number} has been picked up and is on its way.",
            ['type' => 'order_status', 'order_id' => (string) $order->id, 'status' => 'out_for_delivery']
        );

        return response()->json(['data' => $this->formatOrder($order->fresh(['vendorProfile', 'deliveryAddress']))]);
    }

    /** Update delivery status (out_for_delivery → delivered) */
    public function updateStatus(Request $request, Order $order): JsonResponse
    {
        $driver = $this->driver($request);
        if (! $driver || $order->driver_id !== $driver->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data = $request->validate([
            'status' => 'required|in:delivered',
        ]);

        $order->update([
            'status'       => 'delivered',
            'delivered_at' => now(),
        ]);

        $driver->increment('total_deliveries');

        FcmService::notifyUser(
            $order->user_id,
            '✅ Order Delivered',
            "Your order #{$order->order_number} has been delivered. Enjoy!",
            ['type' => 'order_status', 'order_id' => (string) $order->id, 'status' => 'delivered']
        );

        return response()->json(['data' => $this->formatOrder($order->fresh(['vendorProfile', 'deliveryAddress']))]);
    }

    /** Update driver's GPS coordinates */
    public function updateLocation(Request $request): JsonResponse
    {
        $driver = $this->driver($request);
        if (! $driver) return response()->json(['message' => 'Not a driver'], 403);

        $data = $request->validate([
            'latitude'  => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $driver->update([
            'current_latitude'    => $data['latitude'],
            'current_longitude'   => $data['longitude'],
            'location_updated_at' => now(),
        ]);

        return response()->json(['message' => 'Location updated']);
    }

    private function formatOrders($orders): array
    {
        return $orders->map(fn ($o) => $this->formatOrder($o))->values()->all();
    }

    private function formatOrder(Order $o): array
    {
        return [
            'id'           => $o->id,
            'order_number' => $o->order_number,
            'status'       => $o->status,
            'total'        => $o->total,
            'delivery_note'=> $o->delivery_note,
            'created_at'   => $o->created_at?->toIso8601String(),
            'vendor'       => $o->vendorProfile ? [
                'id'            => $o->vendorProfile->id,
                'business_name' => $o->vendorProfile->business_name,
                'region'        => $o->vendorProfile->region,
            ] : null,
            'delivery_address' => $o->deliveryAddress ? [
                'label'    => $o->deliveryAddress->label,
                'district' => $o->deliveryAddress->district,
                'ward'     => $o->deliveryAddress->ward,
                'street'   => $o->deliveryAddress->street,
                'latitude' => $o->deliveryAddress->latitude,
                'longitude'=> $o->deliveryAddress->longitude,
            ] : null,
            'customer' => $o->user ? [
                'name'  => $o->user->full_name,
                'phone' => $o->user->phone,
            ] : null,
            'items_count' => $o->items?->count() ?? 0,
        ];
    }
}
