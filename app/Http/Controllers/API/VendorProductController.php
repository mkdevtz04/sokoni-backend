<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Order;
use App\Models\Product;
use App\Models\VendorProfile;
use App\Services\FcmService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
            'images'      => 'required|array|min:1|max:6',
            'images.*'    => 'image|max:5120',
        ]);

        $images = $request->file('images');
        unset($data['images']);

        $data['vendor_id'] = $this->vendorId($request);
        $data['slug']      = Str::slug($data['name']) . '-' . Str::random(6);

        $product = Product::create($data);
        foreach ($images as $index => $file) {
            $imagePath = $file->store('products', 'public');
            $product->images()->create([
                'image_path' => $imagePath,
                'is_primary' => $index === 0,
                'sort_order' => $index,
            ]);
        }
        $product->load(['images', 'category']);

        // Notify past customers of this vendor about the new product
        try {
            $shopName    = VendorProfile::where('user_id', $product->vendor_id)->value('business_name') ?? 'A vendor';
            $customerIds = Order::where('vendor_id', $product->vendor_id)
                ->distinct()
                ->pluck('user_id');

            foreach ($customerIds as $customerId) {
                app(FcmService::class)->notifyUser(
                    userId: $customerId,
                    title:  "🆕 New from {$shopName}",
                    body:   "{$product->name} is now available",
                    data:   ['type' => 'new_product', 'product_id' => (string) $product->id],
                );
            }
        } catch (\Throwable $e) {
            Log::error('New product notification failed', ['error' => $e->getMessage()]);
        }

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
                'is_primary' => $product->images()->count() === 0,
                'sort_order' => 0,
            ]);
        }

        if ($request->hasFile('new_images')) {
            $existingCount = $product->images()->count();
            foreach ($request->file('new_images') as $index => $file) {
                if ($existingCount >= 6) break;
                $imagePath = $file->store('products', 'public');
                $product->images()->create([
                    'image_path' => $imagePath,
                    'is_primary' => $existingCount === 0 && $index === 0,
                    'sort_order' => $existingCount + $index,
                ]);
                $existingCount++;
            }
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

    public function deleteImage(Request $request, Product $product, int $imageId): JsonResponse
    {
        $this->authorizeProduct($request, $product);

        $image = $product->images()->findOrFail($imageId);
        Storage::disk('public')->delete($image->image_path);
        $wasPrimary = (bool) $image->is_primary;
        $image->delete();

        if ($wasPrimary && $product->images()->count() > 0) {
            $product->images()->orderBy('sort_order')->first()->update(['is_primary' => true]);
        }

        return response()->json(['message' => 'Image removed.']);
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
