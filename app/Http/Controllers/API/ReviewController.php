<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\VendorProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    /**
     * Submit reviews for a delivered order.
     * Expects:
     *   vendor_rating:   int 1-5
     *   vendor_comment:  string optional
     *   products:        array of { product_id, rating, comment }
     */
    public function store(Request $request, Order $order): JsonResponse
    {
        if ($order->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Not found'], 404);
        }

        if ($order->status !== 'delivered') {
            return response()->json(['message' => 'Can only review a delivered order'], 422);
        }

        $data = $request->validate([
            'vendor_rating'          => 'required|integer|min:1|max:5',
            'vendor_comment'         => 'nullable|string|max:500',
            'products'               => 'nullable|array',
            'products.*.product_id'  => 'required|integer|exists:products,id',
            'products.*.rating'      => 'required|integer|min:1|max:5',
            'products.*.comment'     => 'nullable|string|max:500',
        ]);

        $vendorId = $order->vendorProfile?->id
            ?? VendorProfile::where('user_id', $order->vendor_id)->value('id');

        if (! $vendorId) {
            return response()->json(['message' => 'Vendor not found'], 422);
        }

        DB::transaction(function () use ($request, $order, $data, $vendorId) {
            // Vendor review (product_id = null)
            Review::updateOrCreate(
                ['order_id' => $order->id, 'user_id' => $request->user()->id, 'product_id' => null],
                [
                    'vendor_id' => $vendorId,
                    'rating'    => $data['vendor_rating'],
                    'comment'   => $data['vendor_comment'] ?? null,
                ]
            );

            // Per-product reviews
            foreach ($data['products'] ?? [] as $p) {
                Review::updateOrCreate(
                    ['order_id' => $order->id, 'user_id' => $request->user()->id,
                     'product_id' => $p['product_id']],
                    [
                        'vendor_id' => $vendorId,
                        'rating'    => $p['rating'],
                        'comment'   => $p['comment'] ?? null,
                    ]
                );
            }

            // Recalculate vendor aggregate rating
            $this->recalcVendorRating($vendorId);
        });

        return response()->json(['message' => 'Thank you for your review!']);
    }

    public function forVendor(int $vendorId): JsonResponse
    {
        $reviews = Review::with('user')
            ->where('vendor_id', $vendorId)
            ->whereNull('product_id')
            ->latest()
            ->take(50)
            ->get()
            ->map(fn ($r) => $this->format($r));

        return response()->json(['data' => $reviews]);
    }

    public function forProduct(Product $product): JsonResponse
    {
        $reviews = Review::with('user')
            ->where('product_id', $product->id)
            ->latest()
            ->take(50)
            ->get()
            ->map(fn ($r) => $this->format($r));

        return response()->json(['data' => $reviews]);
    }

    /** Check if the authenticated user has already reviewed a given order */
    public function check(Request $request, Order $order): JsonResponse
    {
        if ($order->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $reviewed = Review::where('order_id', $order->id)
            ->where('user_id', $request->user()->id)
            ->exists();

        return response()->json(['reviewed' => $reviewed]);
    }

    private function recalcVendorRating(int $vendorId): void
    {
        $avg = Review::where('vendor_id', $vendorId)
            ->whereNull('product_id')
            ->avg('rating') ?? 0;

        $count = Review::where('vendor_id', $vendorId)
            ->whereNull('product_id')
            ->count();

        VendorProfile::where('id', $vendorId)->update([
            'rating'        => round($avg, 2),
            'total_reviews' => $count,
        ]);
    }

    private function format(Review $r): array
    {
        return [
            'id'         => $r->id,
            'rating'     => $r->rating,
            'comment'    => $r->comment,
            'created_at' => $r->created_at?->toIso8601String(),
            'user'       => [
                'name'   => $r->user?->full_name ?? 'Anonymous',
                'avatar' => $r->user?->profile_picture,
            ],
        ];
    }
}
