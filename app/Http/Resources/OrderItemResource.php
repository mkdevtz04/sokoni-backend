<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'product_id'    => $this->product_id,
            'product_name'  => $this->product_name,
            'product_price' => $this->product_price,
            'product_unit'  => $this->product_unit,
            'quantity'      => $this->quantity,
            'subtotal'      => $this->subtotal,
            'primary_image' => $this->whenLoaded('product', fn () =>
                $this->product?->images?->first()
                    ? asset('storage/' . $this->product->images->first()->image_path)
                    : null
            ),
        ];
    }
}
