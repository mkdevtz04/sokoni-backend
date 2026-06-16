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
            'confirmed_at'         => $this->confirmed_at,
            'preparing_at'         => $this->preparing_at,
            'ready_at'             => $this->ready_at,
            'out_for_delivery_at'  => $this->out_for_delivery_at,
            'delivered_at'         => $this->delivered_at,
            'created_at'       => $this->created_at,
            'payment_reference' => $this->when($this->relationLoaded('payment'), $this->payment?->reference),
            'items'             => OrderItemResource::collection($this->whenLoaded('items')),
            'vendor'            => new VendorResource($this->whenLoaded('vendorProfile')),
            'delivery_address'  => new AddressResource($this->whenLoaded('deliveryAddress')),
            'customer'          => $this->when($this->relationLoaded('user') && $this->user, [
                'id'    => $this->user->id,
                'name'  => trim(($this->user->first_name ?? '') . ' ' . ($this->user->last_name ?? '')),
                'phone' => $this->user->phone,
            ]),
            'driver'            => $this->when(
                $this->relationLoaded('driver') && $this->driver && $this->driver->user,
                fn () => [
                    'id'    => $this->driver->id,
                    'name'  => trim(($this->driver->user->first_name ?? '') . ' ' . ($this->driver->user->last_name ?? '')),
                    'phone' => $this->driver->user->phone,
                ]
            ),
        ];
    }
}
