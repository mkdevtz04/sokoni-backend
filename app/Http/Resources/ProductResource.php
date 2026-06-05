<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'name'            => $this->name,
            'slug'            => $this->slug,
            'description'     => $this->description,
            'price'           => $this->price,
            'sale_price'      => $this->sale_price,
            'effective_price' => $this->effective_price,
            'unit'            => $this->unit,
            'stock'           => $this->stock,
            'is_active'       => $this->is_active,
            'is_featured'     => $this->is_featured,
            'rating'          => $this->rating,
            'total_reviews'   => $this->total_reviews,
            'total_orders'    => $this->total_orders,
            'category'        => new CategoryResource($this->whenLoaded('category')),
            'vendor'          => new VendorResource($this->whenLoaded('vendorProfile')),
            'images'          => ProductImageResource::collection($this->whenLoaded('images')),
            'primary_image'   => $this->when(
                $this->relationLoaded('images'),
                fn () => $this->images->where('is_primary', true)->first()
                    ? asset('storage/' . $this->images->where('is_primary', true)->first()->image_path)
                    : ($this->images->first() ? asset('storage/' . $this->images->first()->image_path) : null)
            ),
        ];
    }
}
