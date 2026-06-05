<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\VendorResource;
use App\Models\Product;
use App\Models\VendorProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $vendors = VendorProfile::where('is_approved', true)
            ->withCount('products')
            ->when($request->search, fn ($q, $s) =>
                $q->where('business_name', 'like', "%{$s}%")
            )
            ->when($request->region, fn ($q, $r) => $q->where('region', $r))
            ->orderByDesc('rating')
            ->paginate(20);

        return response()->json([
            'data' => VendorResource::collection($vendors),
            'meta' => [
                'current_page' => $vendors->currentPage(),
                'last_page'    => $vendors->lastPage(),
                'total'        => $vendors->total(),
            ],
        ]);
    }

    public function show(VendorProfile $vendor): JsonResponse
    {
        $vendor->loadCount('products');

        return response()->json([
            'data' => new VendorResource($vendor),
        ]);
    }

    public function products(Request $request, VendorProfile $vendor): JsonResponse
    {
        $products = Product::where('vendor_id', $vendor->user_id)
            ->where('is_active', true)
            ->with(['images', 'category'])
            ->when($request->category_id, fn ($q, $c) => $q->where('category_id', $c))
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->orderByDesc('total_orders')
            ->paginate(20);

        return response()->json([
            'data' => ProductResource::collection($products),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page'    => $products->lastPage(),
                'total'        => $products->total(),
            ],
        ]);
    }
}
