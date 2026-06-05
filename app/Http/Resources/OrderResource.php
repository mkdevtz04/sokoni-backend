<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'order_number'     => $this->order_number,
            'status'           => $this->status,
            'subtotal'         => $this->subtotal,
            'delivery_fee'     => $this->delivery_fee,
            'total'            => $this->total,
            'payment_status'   => $this->payment_status,
            'payment_method'   => $this->payment_method,
            'delivery_note'    => $this->delivery_note,
            'confirmed_at'     => $this->confirmed_at,
            'delivered_at'     => $this->delivered_at,
            'created_at'       => $this->created_at,
            'items'            => OrderItemResource::collection($this->whenLoaded('items')),
            'vendor'           => new VendorResource($this->whenLoaded('vendorProfile')),
            'delivery_address' => new AddressResource($this->whenLoaded('deliveryAddress')),
        ];
    }
}
