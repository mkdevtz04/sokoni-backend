<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Payment;
use App\Models\UserAddress;
use App\Models\VendorEarning;
use App\Models\VendorProfile;
use App\Services\FcmService;
use App\Services\PalmPesaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $orders = Order::where('user_id', $request->user()->id)
            ->with(['items', 'vendorProfile', 'driver.user'])
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(15);

        return response()->json([
            'data' => OrderResource::collection($orders),
            'meta' => ['current_page' => $orders->currentPage(), 'last_page' => $orders->lastPage()],
        ]);
    }

    public function show(Request $request, Order $order): JsonResponse
    {
        if ($order->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        $order->load(['items.product.images', 'vendorProfile', 'deliveryAddress', 'payment', 'driver.user']);

        return response()->json(['data' => new OrderResource($order)]);
    }

    public function place(Request $request): JsonResponse
    {
        $request->validate([
            'vendor_id'           => ['required', 'exists:users,id'],
            'delivery_address_id' => ['nullable', 'exists:user_addresses,id'],
            'payment_method'      => ['required', 'in:cash,mobile_money,wallet'],
            'delivery_note'       => ['nullable', 'string', 'max:500'],
            'payment_phone'       => ['required_if:payment_method,mobile_money', 'nullable', 'string'],
            'customer_latitude'   => ['nullable', 'numeric'],
            'customer_longitude'  => ['nullable', 'numeric'],
        ]);

        $cart = Cart::where('user_id', $request->user()->id)
            ->with('items.product')
            ->firstOrFail();

        $vendorItems = $cart->items->filter(
            fn ($i) => $i->product && $i->product->vendor_id == $request->vendor_id && $i->product->is_active
        );

        if ($vendorItems->isEmpty()) {
            return response()->json(['message' => 'No items from this vendor in your cart.'], 422);
        }

        return DB::transaction(function () use ($request, $cart, $vendorItems) {
            $subtotal    = $vendorItems->sum(fn ($i) => $i->price * $i->quantity);
            $deliveryFee = $this->calcDeliveryFee(
                (int) $request->vendor_id,
                $request->delivery_address_id
            );
            $total       = $subtotal + $deliveryFee;

            if ($request->payment_method === 'wallet') {
                $wallet = $request->user()->getOrCreateWallet();
                if ($wallet->balance < $total) {
                    return response()->json(['message' => 'Insufficient wallet balance.'], 422);
                }
            }

            $order = Order::create([
                'order_number'        => Order::generateNumber(),
                'user_id'             => $request->user()->id,
                'vendor_id'           => $request->vendor_id,
                'status'              => 'pending',
                'subtotal'            => $subtotal,
                'delivery_fee'        => $deliveryFee,
                'total'               => $total,
                'payment_method'      => $request->payment_method,
                'payment_status'      => 'pending',
                'delivery_address_id' => $request->delivery_address_id,
                'delivery_note'       => $request->delivery_note,
                'customer_latitude'   => $request->customer_latitude,
                'customer_longitude'  => $request->customer_longitude,
            ]);

            foreach ($vendorItems as $item) {
                $order->items()->create([
                    'product_id'    => $item->product_id,
                    'product_name'  => $item->product->name,
                    'product_price' => $item->price,
                    'product_unit'  => $item->product->unit,
                    'quantity'      => $item->quantity,
                    'subtotal'      => $item->price * $item->quantity,
                ]);
                $item->product->decrement('stock', $item->quantity);
                $item->product->increment('total_orders');
            }

            $payment = Payment::create([
                'user_id'  => $request->user()->id,
                'order_id' => $order->id,
                'amount'   => $total,
                'type'     => 'order',
                'method'   => $request->payment_method,
                'status'   => 'pending',
                'phone'    => $request->payment_phone,
            ]);

            if ($request->payment_method === 'wallet') {
                $request->user()->getOrCreateWallet()
                    ->debit($total, "Order #{$order->order_number}", $order->order_number);
                $payment->update(['status' => 'completed']);
                $order->update(['payment_status' => 'paid', 'status' => 'confirmed', 'confirmed_at' => now()]);
            }

            if ($request->payment_method === 'cash') {
                $order->update(['status' => 'confirmed', 'confirmed_at' => now()]);
                $this->recordEarning($order);
            }

            $palmReference = null;
            if ($request->payment_method === 'mobile_money') {
                $user   = $request->user();
                $result = (new PalmPesaService())->initiatePayment(
                    amount:        $total,
                    phone:         $request->payment_phone,
                    orderId:       $order->id,
                    orderNumber:   $order->order_number,
                    customerName:  trim($user->first_name . ' ' . $user->last_name),
                    customerEmail: $user->email ?? '',
                );
                if ($result['success']) {
                    $palmReference = $result['reference'];
                    $payment->update(['reference' => $palmReference]);
                }
            }

            // Notify vendor about the new order
            app(FcmService::class)->notifyUser(
                userId: (int) $request->vendor_id,
                title:  '🛒 New Order Received',
                body:   "Order #{$order->order_number} — TZS " . number_format($total),
                data:   ['type' => 'new_order', 'order_id' => (string) $order->id],
            );

            $cart->items()->whereIn('product_id', $vendorItems->pluck('product_id'))->delete();

            $order->load(['items', 'vendorProfile', 'deliveryAddress', 'payment']);

            return response()->json([
                'message'           => 'Order placed successfully.',
                'data'              => new OrderResource($order),
                'payment_reference' => $palmReference,
            ], 201);
        });
    }

    public function deliveryFee(Request $request): JsonResponse
    {
        $vendorId = (int) $request->validate(['vendor_id' => 'required|integer'])['vendor_id'];
        $lat      = $request->input('lat');
        $lng      = $request->input('lng');

        $vendor = VendorProfile::where('user_id', $vendorId)->first();

        if (!$vendor || !$vendor->latitude || !$vendor->longitude || !$lat || !$lng) {
            return response()->json(['fee' => 0, 'has_coordinates' => false]);
        }

        $distance = $this->haversineKm(
            (float)$vendor->latitude, (float)$vendor->longitude,
            (float)$lat, (float)$lng
        );

        return response()->json([
            'fee'             => $this->feeFromKm($distance),
            'has_coordinates' => true,
            'distance_km'     => round($distance, 1),
        ]);
    }

    private function calcDeliveryFee(int $vendorId, ?int $addressId): int
    {
        $vendor = VendorProfile::where('user_id', $vendorId)->first();
        if (!$vendor || !$vendor->latitude || !$vendor->longitude || !$addressId) {
            return 0;
        }
        $address = UserAddress::find($addressId);
        if (!$address || !$address->latitude || !$address->longitude) {
            return 0;
        }
        $distance = $this->haversineKm(
            (float)$vendor->latitude, (float)$vendor->longitude,
            (float)$address->latitude, (float)$address->longitude
        );
        return $this->feeFromKm($distance);
    }

    private function haversineKm(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $R    = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a    = sin($dLat / 2) ** 2 +
                cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;
        return $R * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    private function feeFromKm(float $km): int
    {
        return min(20000, (int) (2000 + ceil($km) * 500));
    }

    private function recordEarning(Order $order): void
    {
        if (VendorEarning::where('order_id', $order->id)->exists()) {
            return;
        }
        $pct        = (float) config('services.palmpesa.commission_percent', 5);
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

    public function cancel(Request $request, Order $order): JsonResponse
    {
        if ($order->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        if (! in_array($order->status, ['pending', 'confirmed'])) {
            return response()->json(['message' => 'Order cannot be cancelled at this stage.'], 422);
        }

        $order->update(['status' => 'cancelled']);

        foreach ($order->items as $item) {
            if ($item->product) {
                $item->product->increment('stock', $item->quantity);
                $item->product->decrement('total_orders');
            }
        }

        return response()->json(['message' => 'Order cancelled.', 'data' => new OrderResource($order)]);
    }
}
