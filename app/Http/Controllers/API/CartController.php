<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    private function getCart(Request $request): Cart
    {
        return Cart::firstOrCreate(['user_id' => $request->user()->id]);
    }

    public function index(Request $request): JsonResponse
    {
        $cart = $this->getCart($request);
        $cart->load(['items.product.images', 'items.product.vendorProfile']);

        return response()->json(['data' => new CartResource($cart)]);
    }

    public function add(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity'   => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        $product = Product::findOrFail($request->product_id);

        if (! $product->is_active || $product->stock < $request->quantity) {
            return response()->json(['message' => 'Product not available in requested quantity.'], 422);
        }

        $cart = $this->getCart($request);
        $item = $cart->items()->where('product_id', $product->id)->first();

        if ($item) {
            $newQty = $item->quantity + $request->quantity;
            if ($newQty > $product->stock) {
                return response()->json(['message' => 'Not enough stock available.'], 422);
            }
            $item->update(['quantity' => $newQty]);
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'quantity'   => $request->quantity,
                'price'      => $product->effective_price,
            ]);
        }

        $cart->load(['items.product.images', 'items.product.vendorProfile']);

        return response()->json([
            'message' => 'Item added to cart.',
            'data'    => new CartResource($cart),
        ]);
    }

    public function update(Request $request, int $itemId): JsonResponse
    {
        $request->validate(['quantity' => ['required', 'integer', 'min:1', 'max:100']]);

        $cart = $this->getCart($request);
        $item = $cart->items()->findOrFail($itemId);

        if ($item->product->stock < $request->quantity) {
            return response()->json(['message' => 'Not enough stock available.'], 422);
        }

        $item->update(['quantity' => $request->quantity]);
        $cart->load(['items.product.images', 'items.product.vendorProfile']);

        return response()->json([
            'message' => 'Cart updated.',
            'data'    => new CartResource($cart),
        ]);
    }

    public function remove(Request $request, int $itemId): JsonResponse
    {
        $cart = $this->getCart($request);
        $cart->items()->findOrFail($itemId)->delete();
        $cart->load(['items.product.images', 'items.product.vendorProfile']);

        return response()->json([
            'message' => 'Item removed.',
            'data'    => new CartResource($cart),
        ]);
    }

    public function clear(Request $request): JsonResponse
    {
        $this->getCart($request)->items()->delete();

        return response()->json(['message' => 'Cart cleared.']);
    }
}
