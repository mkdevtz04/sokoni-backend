<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $products = Product::where('is_active', true)
            ->with(['images', 'vendorProfile', 'category'])
            ->when($request->search, fn ($q, $s) =>
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%")
            )
            ->when($request->category_id, fn ($q, $c) => $q->where('category_id', $c))
            ->when($request->vendor_id,   fn ($q, $v) => $q->where('vendor_id', $v))
            ->when($request->featured,    fn ($q)     => $q->where('is_featured', true))
            ->when($request->sort === 'price_asc',  fn ($q) => $q->orderBy('price'))
            ->when($request->sort === 'price_desc', fn ($q) => $q->orderByDesc('price'))
            ->when($request->sort === 'rating',     fn ($q) => $q->orderByDesc('rating'))
            ->when(! $request->sort,                fn ($q) => $q->orderByDesc('total_orders'))
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

    public function show(Product $product): JsonResponse
    {
        $product->load(['images', 'vendorProfile', 'category']);

        return response()->json([
            'data' => new ProductResource($product),
        ]);
    }

    public function featured(): JsonResponse
    {
        $products = Product::where('is_active', true)
            ->where('is_featured', true)
            ->with(['images', 'vendorProfile', 'category'])
            ->orderByDesc('rating')
            ->limit(10)
            ->get();

        return response()->json([
            'data' => ProductResource::collection($products),
        ]);
    }
}
