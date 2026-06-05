<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = Category::where('is_active', true)
            ->whereNull('parent_id')
            ->withCount('products')
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'data' => CategoryResource::collection($categories),
        ]);
    }

    public function show(Category $category): JsonResponse
    {
        $category->loadCount('products');

        return response()->json([
            'data' => new CategoryResource($category),
        ]);
    }

    public function products(Request $request, Category $category): JsonResponse
    {
        $products = Product::where('category_id', $category->id)
            ->where('is_active', true)
            ->with(['images', 'vendorProfile'])
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->when($request->sort === 'price_asc',  fn ($q) => $q->orderBy('price'))
            ->when($request->sort === 'price_desc', fn ($q) => $q->orderByDesc('price'))
            ->when($request->sort === 'rating',     fn ($q) => $q->orderByDesc('rating'))
            ->when(! $request->sort,                fn ($q) => $q->orderByDesc('total_orders'))
            ->paginate(20);

        return response()->json([
            'data'  => ProductResource::collection($products),
            'meta'  => [
                'current_page' => $products->currentPage(),
                'last_page'    => $products->lastPage(),
                'total'        => $products->total(),
            ],
        ]);
    }
}
