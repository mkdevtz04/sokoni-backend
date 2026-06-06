<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VendorProductController extends Controller
{
    private function vendorId(Request $request): int
    {
        return $request->user()->id;
    }

    public function index(Request $request): JsonResponse
    {
        $products = Product::where('vendor_id', $this->vendorId($request))
            ->with(['images', 'category'])
            ->latest()
            ->paginate(30);

        return response()->json([
            'data' => ProductResource::collection($products),
            'meta' => ['total' => $products->total()],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $this->ensureApprovedVendor($request);

        $data = $request->validate([
            'name'        => 'required|string|max:150',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0',
            'sale_price'  => 'nullable|numeric|min:0',
            'unit'        => 'required|string|max:30',
            'stock'       => 'required|integer|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'is_active'   => 'boolean',
            'is_featured' => 'boolean',
            'image'       => 'required|image|max:5120',
        ]);

        $imagePath = $request->file('image')->store('products', 'public');
        unset($data['image']);

        $data['vendor_id'] = $this->vendorId($request);
        $data['slug']      = Str::slug($data['name']) . '-' . Str::random(6);

        $product = Product::create($data);
        $product->images()->create([
            'image_path' => $imagePath,
            'is_primary' => true,
            'sort_order' => 0,
        ]);
        $product->load(['images', 'category']);

        return response()->json([
            'message' => 'Product created.',
            'data'    => new ProductResource($product),
        ], 201);
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $this->authorizeProduct($request, $product);

        $data = $request->validate([
            'name'        => 'sometimes|string|max:150',
            'description' => 'nullable|string',
            'price'       => 'sometimes|numeric|min:0',
            'sale_price'  => 'nullable|numeric|min:0',
            'unit'        => 'sometimes|string|max:30',
            'stock'       => 'sometimes|integer|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'is_active'   => 'boolean',
            'is_featured' => 'boolean',
            'image'       => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('image')) {
            $old = $product->images()->where('is_primary', true)->first()
                ?? $product->images()->first();
            if ($old) {
                Storage::disk('public')->delete($old->image_path);
                $old->delete();
            }
            $imagePath = $request->file('image')->store('products', 'public');
            $product->images()->create([
                'image_path' => $imagePath,
                'is_primary' => true,
                'sort_order' => 0,
            ]);
        }

        unset($data['image']);

        if (isset($data['name'])) {
            $data['slug'] = Str::slug($data['name']) . '-' . Str::random(6);
        }

        $product->update($data);

        return response()->json([
            'data' => new ProductResource($product->fresh(['images', 'category'])),
        ]);
    }

    public function destroy(Request $request, Product $product): JsonResponse
    {
        $this->authorizeProduct($request, $product);

        // Delete image files from storage before removing the record
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        $product->forceDelete(); // hard delete — order items already snapshot name/price

        return response()->json(['message' => 'Product deleted.']);
    }

    private function ensureApprovedVendor(Request $request): void
    {
        $vendor = $request->user()->vendorProfile;
        abort_if(! $vendor || ! $vendor->is_approved, 403, 'Vendor not approved.');
    }

    private function authorizeProduct(Request $request, Product $product): void
    {
        abort_if($product->vendor_id !== $this->vendorId($request), 403, 'Unauthorized.');
    }
}
