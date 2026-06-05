<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $product = $this->whenLoaded('product');

        return [
            'id'         => $this->id,
            'product_id' => $this->product_id,
            'quantity'   => $this->quantity,
            'price'      => $this->price,
            'subtotal'   => $this->subtotal,
            'product'    => new ProductResource($product),
        ];
    }
}
